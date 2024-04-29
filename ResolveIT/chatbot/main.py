#! C:\Users\logic\AppData\Local\Programs\Python\Python39\python.exe
import nltk
#nltk.download('punkt')
from nltk.stem.lancaster import LancasterStemmer
stemmer = LancasterStemmer()

import numpy
import scipy
import tflearn
import tensorflow as tf
import random
import json
import pickle
import pymysql
import time

try:
    with open("C:\\xampp\\htdocs\\ResolveIT\\chatbot\\intents.json") as file:
        data = json.load(file)
except:
    print("file not found")

try:
    with open("C:\\xampp\\htdocs\\ResolveIT\\chatbot\\data.pickle", "rb") as f:
        words, labels, training, output = pickle.load(f)
except:
    words = []
    labels = []
    docs_x = []
    docs_y = []

    for intent in data["intents"]:
        for pattern in intent["patterns"]:
            wrds = nltk.word_tokenize(pattern)
            words.extend(wrds)
            docs_x.append(wrds)
            docs_y.append(intent["tag"])

        if intent["tag"] not in labels:
            labels.append(intent["tag"])

    words = [stemmer.stem(w.lower()) for w in words if w != "?"]
    words = sorted(list(set(words)))

    labels = sorted(labels)

    training = []
    output = []

    out_empty = [0 for _ in range(len(labels))]

    for x, doc in enumerate(docs_x):
        bag = []

        wrds = [stemmer.stem(w.lower()) for w in doc]

        for w in words:
            if w in wrds:
                bag.append(1)
            else:
                bag.append(0)

        output_row = out_empty[:]
        output_row[labels.index(docs_y[x])] = 1

        training.append(bag)
        output.append(output_row)


    training = numpy.array(training)
    output = numpy.array(output)

    with open("data.pickle", "wb") as f:
        pickle.dump((words, labels, training, output), f)

tf.compat.v1.reset_default_graph()

net = tflearn.input_data(shape=[None, len(training[0])])
net = tflearn.fully_connected(net, 8)
net = tflearn.fully_connected(net, 8)
net = tflearn.fully_connected(net, len(output[0]), activation="softmax")
net = tflearn.regression(net)

model = tflearn.DNN(net)

try:
    model.load("C:\\xampp\\htdocs\\ResolveIT\\chatbot\\model.tflearn")
except:
    model.fit(training, output, n_epoch=1500, batch_size=8, show_metric=True)
    model.save("C:\\xampp\\htdocs\\ResolveIT\\chatbot\\model.tflearn")

def bag_of_words(s, words):
    bag = [0 for _ in range(len(words))]
    s_words = nltk.word_tokenize(s)
    s_words = [stemmer.stem(word.lower()) for word in s_words]

    for se in s_words:
        for i, w in enumerate(words):
            if w == se:
                bag[i] = 1

    return numpy.array(bag)

def getAllTickets(conn):
    try:
        with conn.cursor() as cursor:
            sql = "SELECT * FROM `tickets`"
            cursor.execute(sql)
            rows = cursor.fetchall()

            for row in rows:
                print(row)
    finally:
        conn.close()

def getTicket(conn):
    try:
        with conn.cursor() as cursor:
            with open("C:\\xampp\\htdocs\\ResolveIT\\currentID.txt", "r") as f:
                string = f.read()

            ticketID = int(string)
            sql = "SELECT * FROM `tickets` WHERE id = %s"
            cursor.execute(sql, ticketID)
            ticket = cursor.fetchone()
            print(ticket['msg'])
    finally:
        conn.close()

def chat(conn):
    with conn.cursor() as cursor:
        #with open("C:\\Users\\Kumi\\Documents\\School\\Spring2024\Capstone Project\\Team_7_CSCI_4940\\ResolveIT\\currentID.txt", "r") as f:
        with open("C:\\xampp\\htdocs\\ResolveIT\\currentID.txt", "r") as f:
            string = f.read()

        botName = "ChattyAI"
        ticketID = int(string)
        sql = "SELECT * FROM `tickets` WHERE id = %s"
        cursor.execute(sql, ticketID)
        ticket = cursor.fetchone()
        print(ticket['msg'])

        inp = ticket["msg"]
        results = model.predict([bag_of_words(inp, words)])[0]
        results_index = numpy.argmax(results)
        tag = labels[results_index]

        if results[results_index] > 0.7:
            for tg in data["intents"]:
                if tg["tag"] == tag:
                    responses = tg["responses"]
            print(random.choice(responses))
            print(ticketID)
            sql = "INSERT INTO `tickets_comments` (username, ticket_id, msg) VALUES (%s, %s, %s)"
            cursor.execute(sql, (botName, ticketID, random.choice(responses)))

            conn.commit()
            conn.close()
        else:
            elevateTicket = "SELECT * FROM `users` WHERE `role` = 'admin' ORDER BY RAND() LIMIT 1"
            cursor.execute(elevateTicket)
            admin = cursor.fetchone()
            print(admin["username"])
            adminSelected = admin['username']


            failedResponse = "I am unable to understand what you need assistance with at this time. I will elevate your ticket to an admin. Ticket reassigned to " + adminSelected + ". Thank you!"
            print(failedResponse)

            sql = "INSERT INTO `tickets_comments` (username, ticket_id, msg) VALUES (%s, %s, %s)"
            cursor.execute(sql, (botName, ticketID, failedResponse))

            time.sleep(1)

            assignAdmin = "UPDATE `tickets` SET `assigned` = %s"
            cursor.execute(assignAdmin, adminSelected)

            conn.commit()

            #sql2 = 'SELECT * FROM `tickets_comments` WHERE ticket_id = %s ORDER BY created DESC LIMIT 1'
            #cursor.execute(sql2, ticketID)
            #comment = cursor.fetchone()
            #print(comment["msg"])
            #lastComment = comment["msg"]

            #results = model.predict([bag_of_words(lastComment, words)])[0]
            #results_index = numpy.argmax(results)
            #tag = labels[results_index]
            #for tg in data["intents"]:
                #if tg["tag"] == tag:
                    #responses = tg["responses"]
                    #if tg["tag"] == "elevate ticket":
                        #elevate = "Understood. Elevating ticket to an admin..."
                        #print(elevate)
                    #else:
                        #notElevate = "Understood. I will not elevate the ticket. Have a good day!"
                        #print(notElevate)

        conn.close()

conn = pymysql.connect(
    host="localhost",
    user="root",
    password="admin",
    database="phpticket",
    cursorclass=pymysql.cursors.DictCursor
    )

chat(conn)