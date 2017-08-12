"""
File:                 Telegram.py

This file contains general Telegram methods. These methods are not limited
to a certain action.

class:
    Telegram -- general Telegram methods

"""

import time
from General import General
from Database import Database

class Telegram(object):
    """
    General Telegram methods
    """
    def __init__(self):
        self.database = Database()
        self.general = General()

    def check_user_blacklist(self, user_id):
        """
        Method to check if the user is blacklisted.
        :params user_id: the id of the user
        :returns: True if user is blocked, False if not
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_user_blacklist.__name__,
            'action="Method called", user_id="{}"'.format(user_id)
        )
        start_time = time.time()

        query = "SELECT * FROM users WHERE user_id = ? AND blocked = 1"
        values = (user_id, )
        results = self.database.select_handler(query, values)
        blocked = False
        if len(results) > 0:
            blocked = True

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_user_blacklist.__name__,
            log_string
        )

        return blocked

    def register_user(self, user_id, user_name, first_name, last_name):
        """
        This method checks if the user is registered, if not, it will register
        the user.
        """
        query = "SELECT * FROM users WHERE user_id = ?"
        values = (user_id,)
        results = self.database.select_handler(query, values)
        # check if the query has returned any results, if so we are done
        # else we need to create the user.
        if len(results) > 0:
            return

        # create the user
        query = "INSERT INTO users (user_id, user_name, first_name, last_name)"\
              + "VALUES (?, ?, ?, ?)"
        values = [(user_id, user_name, first_name, last_name)]
        self.database.update_handler(query, values)


