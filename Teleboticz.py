"""
File:                 Teleboticz.py

This file contains the the main Teleboticz Class. It is responsible
for creating the Telegram Bot.

class:
    Teleboticz -- resposible for creating the bot

    methods:
        __init__
            Initiate this class.



---- Changelog ----

Version:              0.1
Date:                 06-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import sys
import os
import time
import datetime
from General import General
from Database import Database
import telepot
import telepot.helper
from telepot.loop import MessageLoop
from telepot.delegate import (
    per_chat_id,
    per_inline_from_id,
    create_open,
    pave_event_space,
    intercept_callback_query_origin,
    include_callback_query_chat_id
)
import TelegramChatHandler
import TelegramInlineQueryHandler

class Teleboticz(object):
    """
    The Teleboticz class, see description above.
    """
    def __init__(self):
        self.general = General()
        self.database = Database()
        self.pid_file = self.general.fix_real_path(sys.argv[0] + ".pid")
        self.bot = object
        self.running = self.check_running()
        if not self.running:
            try:
                # start the bot
                self.start_bot()
                self.update_pid()
                while 1:
                    # update the pid file every 10 seconds
                    self.general.logger(
                        3,
                        self.__class__.__name__,
                        self.__init__.__name__,
                        'action="bot is (still) running", time={}'.format(str(datetime.datetime.now())))
                    time.sleep(30)
                    self.update_pid()
            finally:
                # remove the pid file
                os.unlink(self.pid_file)
        else:
            # pid file does exist, don't start
            print "Bot seems to be running. Exiting"
            sys.exit(0)

    def template_method(self):
        """
        Template method
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.template_method.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # do something here...

        # this should be placed at the bottom of the method
        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.template_method.__name__,
            log_string
        )

    def check_running(self):
        """
        Method to check if the bot is currently running. The pid file is updated every
        60 seconds. If the pid file is older than 60 seconds, the bot isn't running.
        :returns: True if the bot is running, else this method returns False
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_running.__name__,
            'action="Method called"'
        )
        start_time = time.time()
        running = False
        if os.path.exists(self.pid_file):
            # the pid_file seems to exist
            # get the age of the file
            current_time = time.time()
            file_time = os.path.getmtime(self.pid_file)
            age = current_time - file_time

            # if the file is older than 60 seconds, we can conclude that the bot isn't running
            if age < float(60):
                message = "The pid file exists and is been editited in the last 60 seconds.\n"
                message += "This means that the bot is probably running. If this is not the case,\n"
                message += "please delete the file '{}'"
                print message.format(self.pid_file)
                # write to the log file
                log_string = 'result="Bot is running", pid_file="{}", age="{}"'
                log_string = log_string.format(self.pid_file, age)
                self.general.logger(
                    2,
                    self.__class__.__name__,
                    self.check_running.__name__,
                    log_string
                )
                running = True
            else:
                # the file is older than 60 seconds, so the bot is recently stopped
                message = "The pid file '{0}' exists, but seems to be old. "
                message += "This probably means the Bot isn't running.\n"
                message += "Removing the file '{0}'..\n\n"
                message += "After removing the pid file the bot can be started."
                print message.format(self.pid_file)
                os.unlink(self.pid_file)
                log_string = 'result="Bot is not running", '
                log_string += 'pid_file="{}", age="{}", '
                log_string += 'action="The file is removed"'
                log_string = log_string.format(self.pid_file, age)
                self.general.logger(
                    2,
                    self.__class__.__name__,
                    self.check_running.__name__,
                    log_string
                )
                running = False
        else:
            # The file doesn't exist, let's assume the bot isn't running.
            log_string = 'result="The bot not running", pid_file="{}", exists="False"'
            self.general.logger(
                2,
                self.__class__.__name__,
                self.check_running.__name__,
                log_string
            )
            running = False
        # finishing the method
        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", return="{}"'.format(
            execution_time,
            running
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_running.__name__,
            log_string
        )
        return running

    def start_bot(self):
        """Function to start the bot"""
        self.general.logger(
            3,
            self.__class__.__name__,
            self.start_bot.__name__,
            'action="method called"')

        self.bot = telepot.DelegatorBot(self.general.config.get("Telegram", "api_key"), [
            intercept_callback_query_origin(
                pave_event_space()
            )
            (
                per_inline_from_id(),
                create_open,
                TelegramInlineQueryHandler.TelegramInlineQueryHandler,
                timeout=30
            ),
            include_callback_query_chat_id(
                pave_event_space()
            )
            (
                per_chat_id(
                    types=['private']
                ),
                create_open,
                TelegramChatHandler.TelegramChatHandler,
                timeout=30
            ),
        ])
        #self.bot.message_loop(run_forever='Listening ...')
        # running the bot as a thread to prevent blocking other actions
        # this way we can update the pid file every x seconds
        MessageLoop(self.bot).run_as_thread()
        self.general.logger(
            2,
            self.__class__.__name__,
            self.start_bot.__name__,
            'action="bot started"')
        print 'Listening ...'

    def update_pid(self):
        """
        Method to update the pid file (and write the current pid to the file)
        """
        pid = str(os.getpid())
        pid_file = open(self.pid_file, 'w')
        pid_file.write(pid)
        pid_file.close()
        return

