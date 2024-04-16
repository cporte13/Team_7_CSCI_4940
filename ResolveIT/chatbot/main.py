import nltk
from nltk.stem.lancaster import LancasterStemmer
stemmer = LancasterStemmer()

import numpy
import tflearn
import tensorflow as tf
import random
import json
import pickle
import pymysql

with open("intents.json") as file:
    data = json.load(file)

try:
    with open("data.pickle", "rb") as f:
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

model.fit(training, output, n_epoch=1500, batch_size=8, show_metric=True)
model.save("model.tflearn")

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
            with open("C:\\Users\\Kumi\\Documents\\School\\Spring2024\Capstone Project\\Team_7_CSCI_4940\\ResolveIT\\currentID.txt", "r") as f:
                string = f.read()

            ticketID = int(string)
            sql = "SELECT * FROM `tickets` WHERE id = %s"
            cursor.execute(sql, ticketID)
            ticket = cursor.fetchone()
            print(ticket['msg'])
    finally:
        conn.close()

def chatTesting(conn):
    with conn.cursor() as cursor:
        with open("C:\\Users\\Kumi\\Documents\\School\\Spring2024\Capstone Project\\Team_7_CSCI_4940\\ResolveIT\\currentID.txt", "r") as f:
            string = f.read()

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
        else:
            print("I don't quite understand. Please ask another question.")

def chat(conn):
    with conn.cursor() as cursor:
        with open("C:\\Users\\Kumi\\Documents\\School\\Spring2024\Capstone Project\\Team_7_CSCI_4940\\ResolveIT\\currentID.txt", "r") as f:
            string = f.read()

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

            sql = "INSERT INTO `tickets_comments` (ticket_id, msg) VALUES (%s, %s)"
            cursor.execute(sql, (ticketID, random.choice(responses)))
            conn.commit()
            conn.close()
        else:
            print("I don't quite understand. Please ask another question.")

conn = pymysql.connect(
    host="localhost",
    user="root",
    password="admin",
    database="phpticket",
    cursorclass=pymysql.cursors.DictCursor
    )

#chatTesting(conn)
chat(conn)