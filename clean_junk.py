"""
Please be carefull when calling this script, it will delete all temporary data.
Also it will clean the database.
"""

import glob
import os
import sqlite3

# clean the temp files
extensions = ('*.pyc', '*.log', '*.pid')

for ext in extensions:
    for file in glob.glob(ext):
        print "delete file: {}".format(file)
        os.unlink(file)

# clean database junk
query_callback_queries = "DELETE FROM telegram_callback_queries"
query_received_chat_messages = "DELETE FROM telegram_chat_messages"
query_inline_queries = "DELETE FROM telegram_inline_queries"
query_send_chat_messages = "DELETE FROM telegram_send_messages"
query_users = "DELETE FROM telegram_users"

queries = (
    query_callback_queries,
    query_received_chat_messages,
    query_inline_queries,
    query_send_chat_messages,
    query_users
)

conn = sqlite3.connect('teleboticz.db')
cursor = conn.cursor()

for query in queries:
    print "execute query: {}".format(query)
    cursor.execute(query)
    print "affected rows: {}".format(cursor.rowcount)

conn.commit()
conn.close()