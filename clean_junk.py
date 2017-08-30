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

for ext in extensions:
    for file in glob.glob('./PythonClasses/' + ext):
        print "delete file: {}".format(file)
        os.unlink(file)

# clean database tables
tables = []
#tables.append('telegram_callback_queries')
tables.append('telegram_chat_messages')
tables.append('telegram_inline_queries')
tables.append('telegram_send_messages')
tables.append('telegram_users')

conn = sqlite3.connect('database/teleboticz.db')
cursor = conn.cursor()

for table in tables:
    query = "DELETE FROM {}".format(table)
    print "Executing query: '{}'"
    cursor.execute(query)
    print "Affected rows: {}".format(cursor.rowcount)


conn.commit()
conn.close()