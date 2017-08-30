"""
File:                 TeleboticzHandler.py

This file contains the methods required to handle the registered Teleboticz
requests. It uses the database to dertermine which requests need to be handled.

class:
    TeleboticzHandler -- resposible for handling the bot requests

    methods:
        __init__
            Initiate this class.



---- Changelog ----

Version:              0.1
Date:                 06-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import time
import json
from datetime import datetime
from Database import Database
from General import General
from Domoticz import Domoticz
from TeleboticzGeneral import TeleboticzGeneral
from telepot.namedtuple import InlineKeyboardButton

class TeleboticzHandler(object):
    """
    The TeleboticzHandler class
    """
    def __init__(self):
        self.database = Database()
        self.general = General()
        self.domoticz = Domoticz()
        self.teleboticzgeneral = TeleboticzGeneral()
        return

    def handle_chat_messages(self):
        """
        Method to handle the incoming chat messages. These are messages which are handled
        by the TelegramChatHandler class. 
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_chat_messages.__name__,
            'action="Method called"'
        )
        start_time = time.time()
        
        query = "SELECT id, chat_id, message FROM telegram_chat_messages "\
              + "WHERE date_handled = 0 ORDER BY date_in ASC"
        results = self.database.select_handler(query)
        for row in results:
            row_id = row[0]
            chat_id = str(row[1])
            command = row[2]

            log_string = 'action="handling command \'{}\' from \'{}\'"'.format(
                command,
                chat_id
            )
            self.general.logger(
                2,
                self.__class__.__name__,
                self.handle_chat_messages.__name__,
                log_string
            )

            # use the general function to determine what to do with this command
            kwargs = {'chat_id': chat_id, 'command': command}

            self.determine_chat_method(**kwargs)

            # now update the handled chat message
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            query = "UPDATE telegram_chat_messages SET date_handled = ? WHERE id = ?"
            values = [(timestamp, row_id)]
            self.database.update_handler(query, values)

            log_string = 'action="command \'{}\' from \'{}\' handled"'.format(
                command,
                chat_id
            )
            self.general.logger(
                2,
                self.__class__.__name__,
                self.handle_chat_messages.__name__,
                log_string
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_chat_messages.__name__,
            log_string
        )
        return

    def determine_chat_method(self, **kwargs):
        """
        This method determines which method should be called based on the given command
        """
        def func_not_found(**kwargs):
            """
            If the method could not be found
            """
            # log this action
            log_string = 'result="method {} not found"'.format(kwargs['command'])
            self.general.logger(
                1,
                self.__class__.__name__,
                self.determine_chat_method.__name__,
                log_string
            )
            # to send a message to the user, we need a chat_id
            # if this is not found, we can't send the message
            message_to_send = self.general.translate_text('unknown_command')
            if 'chat_id' in kwargs:
                self.teleboticzgeneral.send_chat_message(kwargs['chat_id'], message_to_send)

            return False

        log_string = 'action="Method called", kwargs="{}"'.format(
            str(kwargs)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_chat_method.__name__,
            log_string
        )
        start_time = time.time()

        # to work properly, this method needs a command
        # so check if the command parameter is given
        keys_list = ['command']
        if not self.general.check_kwargs(keys_list, kwargs):
            return False

        # determine the correct chat method
        method_name = 'handle_command_' + kwargs['command']
        func = getattr(self, method_name, func_not_found)
        result = func(**kwargs)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", result="{}"'.format(
            execution_time,
            result
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_chat_method.__name__,
            log_string
        )

        return result

    def handle_command_scenes(self, **kwargs):
        """
        Method to create and send the scenes to the telegram client
        :param **kwargs: should contain the chat_id
        """
        log_string = 'action="Method called", kwargs="{}"'.format(
            str(kwargs)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_command_scenes.__name__,
            log_string
        )
        start_time = time.time()

        # check if the kwargs parameter contains all the required parameters
        keys_list = ['chat_id']
        if not self.general.check_kwargs(keys_list, kwargs):
            return
        
        chat_id = kwargs['chat_id']

        # getting the latest status of the scenes
        self.domoticz.get_domoticz_info()
        if 'scenes' not in self.domoticz.domoticz_results:
            message_to_send = self.general.translate_text('scenes_not_found')
            self.teleboticzgeneral.send_chat_message(chat_id, message_to_send)
            return

        # create the options from the scene results
        button_list = []
        for idx in self.domoticz.domoticz_results['scenes']:
            button_list.append(
                InlineKeyboardButton(
                    text=self.domoticz.domoticz_results['scenes'][idx]['name'],
                    callback_data='method=load_actions;device_type=scenees;idx=' + idx
                )
            )

        message = self.general.translate_text('found_scenes')
        # use the send_buttons method to send the buttons (doh)
        self.teleboticzgeneral.send_buttons(chat_id, message, button_list)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_command_scenes.__name__,
            log_string
        )

        return

    def handle_command_switches(self, **kwargs):
        """
        Method to create and send the switches to the telegram client
        :param **kwargs: should contain the chat_id
        """
        log_string = 'action="Method called", kwargs="{}"'.format(
            str(kwargs)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_command_switches.__name__,
            log_string
        )
        start_time = time.time()

        # check if the kwargs parameter contains all the required parameters
        keys_list = ['chat_id']
        if not self.general.check_kwargs(keys_list, kwargs):
            return

        chat_id = kwargs['chat_id']

        # getting the latest status of the scenes
        self.domoticz.get_domoticz_info()
        if 'switches' not in self.domoticz.domoticz_results:
            message_to_send = self.general.translate_text('switches_not_found')
            self.teleboticzgeneral.send_chat_message(chat_id, message_to_send)
            return

        # create the options from the scene results
        button_list = []
        for idx in self.domoticz.domoticz_results['switches']:
            button_list.append(
                InlineKeyboardButton(
                    text=self.domoticz.domoticz_results['switches'][idx]['name'],
                    callback_data='method=load_actions;device_type=switches;idx=' + idx
                )
            )

        message = self.general.translate_text('found_switches')
        # use the send_buttons method to send the buttons (doh)
        self.teleboticzgeneral.send_buttons(chat_id, message, button_list)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_command_switches.__name__,
            log_string
        )

        return

    def handle_callback_queries(self):
        """
        Method to handle the incoming callback querues. These are queries which are handled
        by the TelegramChatHandler class.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_chat_messages.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # get the callback queries from the database
        query = "SELECT id, msg_id, user_id, data, query_id FROM telegram_callback_queries "\
              + "WHERE date_handled = 0 ORDER BY date_in ASC"
        results = self.database.select_handler(query)

        # loop through the results
        for row in results:
            row_id = row[0]
            msg_id = row[1]
            user_id = row[2]
            data = row[3]
            query_id = row[4]

            # add the ids to the kwargs dict
            kwargs = {}
            kwargs['msg_id'] = msg_id
            kwargs['user_id'] = user_id
            kwargs['query_id'] = query_id
            
            # now split the data string to key/value pairs
            key_value_pairs = data.split(';')

            # append the key value pairs to the kwargs dictionary
            for key_value_pair in key_value_pairs:
                key, value = key_value_pair.split('=')
                kwargs[key] = value
            
            print json.dumps(kwargs, sort_keys=True, indent=4)

            self.determine_callback_method(**kwargs)

            # now update the handled chat message
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            query = "UPDATE telegram_callback_queries SET date_handled = ? WHERE id = ?"
            values = [(timestamp, row_id)]
            #self.database.update_handler(query, values)

            log_string = 'action="command \'{}\' from \'{}\' handled"'.format(
                data,
                user_id
            )
            self.general.logger(
                2,
                self.__class__.__name__,
                self.handle_command_switches.__name__,
                log_string
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_command_switches.__name__,
            log_string
        )
        return

    def determine_callback_method(self, **kwargs):
        """
        This method is responsible for calling the right method based on the callback
        query.
        :param kwargs: parameters for this method in a key/value dict
        :returns: returns the result from the callced action
        """
        def func_not_found(**kwargs):
            """
            If the method could not be found
            :param kwargs: parameters for this method in a key/value dict
            """
            # log this action
            log_string = 'result="method {} not found"'.format(kwargs['method'])
            self.general.logger(
                1,
                self.__class__.__name__,
                self.determine_callback_method.__name__,
                log_string
            )
            # to send a message to the user, we need a chat_id
            # if this is not found, we can't send the message
            message_to_send = self.general.translate_text('unknown_command')
            if 'user_id' in kwargs:
                self.teleboticzgeneral.send_chat_message(str(kwargs['user_id']), message_to_send)

            return False

        log_string = 'action="Method called", kwargs="{}"'.format(
            str(kwargs)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_chat_method.__name__,
            log_string
        )
        start_time = time.time()

        # to work properly, this method needs a command
        # so check if the command parameter is given
        
        # check if all the required parameters are set in the kwargs
        keys_list = ['method']

        if not self.general.check_kwargs(keys_list, kwargs):
            return False

        # determine the correct chat method
        method_name = 'handle_callback_' + kwargs['method']
        func = getattr(self, method_name, func_not_found)
        result = func(**kwargs)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", result="{}"'.format(
            execution_time,
            result
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_callback_method.__name__,
            log_string
        )

        return result

    def handle_callback_load_actions(self, **kwargs):
        """
        This method handles the load_actions command. This command is used to load the possible
        actions for the selected switch/scene/group.
        :param kwargs: parameters for this method in a key/value dict
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_callback_load_actions.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # check if all the required parameters are set in the kwargs
        keys_list = ['device_type', 'idx', 'msg_id', 'user_id', 'query_id']
        if not self.general.check_kwargs(keys_list, kwargs):
            return

        # update the result set
        self.domoticz.get_domoticz_info()

        # get the necessary information
        try:
            domoticz_type = self.domoticz.domoticz_results[kwargs['device_type']][kwargs['idx']]['type']
            status = self.domoticz.domoticz_results[kwargs['device_type']][kwargs['idx']]['status']
            name = self.domoticz.domoticz_results[kwargs['device_type']][kwargs['idx']]['name']
        except KeyError as error:
            message = self.general.translate_text('no_possible_actions')
            self.teleboticzgeneral.answer_callback_query(kwargs['query_id'], message)
            
            log_string = 'error="{}", description="{}"'.format(
                str(error),
                "device_type not found")
            self.general.logger(
                1,
                self.__class__.__name__,
                self.handle_callback_load_actions.__name__,
                log_string)
            return

        # get the possible commands for this device type
        try:
            possible_commands_array = \
                self.general.configuration['ACTIONS'][domoticz_type].split(',')
        except KeyError as error:
            self.bot.answerCallbackQuery(
                query_id,
                text=self.general.translate_text('no_possible_actions'))
            log_string = 'error="{}", description="{}"'.format(
                str(error),
                "No actions defined in the config file for this device type")
            self.general.write_to_log(
                1,
                self.__class__.__name__,
                self.load_actions.__name__,
                log_string)
            return



        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_callback_load_actions.__name__,
            log_string
        )
        return