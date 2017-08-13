"""
File:                 Database.py

This file contains the methods used to use the database

class:
    Database -- responsible to handle all the database actions.

    methods:
        __init__
            Initiate this class.
            Loads the configuration and translation by default
        connect_db
            This method connects to the sqlite database and sets the
            cursor
        update_handler
            This method handles all the queries used to alter the database
            such as INSERT, UPDATE and DELETE.
        select_handler
            This method handles the SELECT queries.

---- Changelog ----

Version:              0.1
Date:                 05-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import sqlite3
import time
from General import General

class Database(object):
    """
    Class to handle database interactions
    """
    def __init__(self):
        self.general = General()
        self.database = self.general.fix_real_path('../teleboticz.db')
        self.conn = None
        self.cursor = None

    def connect_db(self):
        """
        Method to connect to the database and set the cursor
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.connect_db.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        self.conn = sqlite3.connect(self.database)
        self.cursor = self.conn.cursor()
        self.general.logger(
            2,
            self.__class__.__name__,
            self.connect_db.__name__,
            'result="Connected successfully to ' + self.database + '"'
        )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.connect_db.__name__,
            log_string
        )

    def update_handler(self, query, values):
        """
        Method to update the database (INSERT, UPDATE and DELETE)
        :param query: The (prepared) query to run
        :param values: The actual values to use (an array of arrays)
        """
        log_string = 'action="Method called", query="{}", values="{}"'.format(
            query,
            str(values)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.update_handler.__name__,
            log_string
        )
        start_time = time.time()
        self.connect_db()
        try:
            self.cursor.executemany(query, values)
            self.conn.commit()
            self.conn.close()
            self.general.logger(
                2,
                self.__class__.__name__,
                self.update_handler.__name__,
                'result="Query executed successfully"'
            )
        except sqlite3.Error as error:
            self.general.logger(
                0,
                self.__class__.__name__,
                self.update_handler.__name__,
                'error="' + str(error) + '"'
            )
        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.update_handler.__name__,
            log_string
        )
        return

    def select_handler(self, query, values=None):
        """
        Method to get results from the database
        :param query: The (prepared) query to run
        :param values: The actual values to use (an array of arrays)
        :returns: a list with the result
        """
        log_string = 'action="Method called", query="{}", values="{}"'.format(
            query,
            str(values)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.select_handler.__name__,
            log_string
        )
        start_time = time.time()
        # connect to the database
        self.connect_db()

        # try to run the query
        results = None
        try:
            if not values is None:
                self.cursor.execute(query, values)
            else:
                self.cursor.execute(query)
            results = self.cursor.fetchall()
            self.conn.close()
            self.general.logger(
                2,
                self.__class__.__name__,
                self.select_handler.__name__,
                'result="Query executed successfully"'
            )
        except sqlite3.Error as error:
            self.general.logger(
                0,
                self.__class__.__name__,
                self.select_handler.__name__,
                'error="' + str(error) + '"'
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.select_handler.__name__,
            log_string
        )
        return results
