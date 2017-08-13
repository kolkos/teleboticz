"""
File:                 General.py

This file contains the general methods which are used by the telegram bot.

class:
    General -- responsible for all the general methods of the Telegram bot

    methods:
        __init__
            Initiate this class.
            Loads the configuration and translation by default
        fix_real_path
            A fix to use the right path (includes the current dir)
        load_config
            The method to load the configuration
        dump_config
            A method to create a dump from the current config
        logger
            This is the method used to log to a file

---- Changelog ----

Version:              0.1
Date:                 05-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import ConfigParser
import os
import io
import sys
from datetime import datetime
import time

class General(object):
    """
    General functions for Teleboticz
    """

    def __init__(self):
        self.config = {}
        self.config_file = self.fix_real_path('config.ini')
        self.translation_file = self.fix_real_path('translation.ini')
        self.log_file = self.fix_real_path(sys.argv[0] + ".log")
        self.load_config()
        self.load_translation()

    @staticmethod
    def fix_real_path(file_name):
        """
        Retrieves the full path of the file
        """
        real_path = os.path.join(
            os.path.abspath(
                os.path.dirname(
                    __file__
                )
            ),
            file_name
        )
        return real_path

    def load_config(self):
        """
        Method to load the config file
        """
        with open(self.config_file) as f:
            file_contents = f.read()
        self.config = ConfigParser.RawConfigParser(allow_no_value=True)
        self.config.readfp(io.BytesIO(file_contents))

    def dump_config(self):
        """
        Create a dump of the current configuration
        """
        config_string = ""
        config_string += "Config File: " + self.config_file + ":\n"
        for section in self.config.sections():
            config_string += "Section: {}\n".format(section)
            for option in self.config.options(section):
                config_string += "\t{:15}: {}\n".format(option, self.config.get(section, option))

        self.logger(
            3,
            self.__class__.__name__,
            self.dump_config.__name__,
            config_string)


    def logger(self, priority, class_name, method_name, text_to_log):
        """
        Method to write to the log file.abs

        There are currently four priorities defined:
        0 = Error   -> Something went wrong, code could not continue.
        1 = Warning -> Something bad happened, the code could continue,
                       results may be nog what is expected.
        2 = Info    -> Comfirmation that a action is executed successfully
        3 = Debug   -> For debugging purposes

        This method checks the configuration file if the line should be logged.

        :param priority: The number must match one of the priorities above
        :param class_name: The name of the class from where the logger is called
        :param method_name: The name of the method from where the logger is called
        :param text_to_log: The actual text to log
        """
        # check if the line should be logged
        if priority <= int(self.config.get('Teleboticz', 'log_level')):
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f")

            # translate the priority to a readable text
            priority_def = ['Error', 'Warning', 'Info', 'Debug']
            priority_text = priority_def[priority]

            # define the log string pattern
            pattern = '{}, priority="{}", class="{}", method="{}", {}\n'

            # create the string to log
            log_string = pattern.format(
                timestamp,
                priority_text,
                class_name,
                method_name,
                text_to_log
            )

            # write to the log file
            try:
                log = open(self.log_file, 'a+')
                log.write(log_string)
                log.close()
            except IOError as error:
                print error

        return

    def load_translation(self):
        """
        Method to load the translation from the translation file
        :returns: nothing
        """
        log_string = 'action="method called"'
        self.logger(
            3,
            self.__class__.__name__,
            self.load_translation.__name__,
            log_string
        )
        start_time = time.time()

        config = ConfigParser.ConfigParser()
        config.optionxform = str
        config.read(self.translation_file)

        self.translation = {}
        self.translation = dict(config.items(self.config.get('Teleboticz', 'language')))
        
        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.logger(
            3,
            self.__class__.__name__,
            self.translate_text.__name__,
            log_string
        )

        return

    def translate_text(self, translation_key, variables=None):
        """
        This method handles the translation

        The first argument is the key that is used to store the translation in the ini file
        the second argument is an dictionary, by default this dictionary isn't set
        the keys in the dictionary must be the same as the placeholders in the translation.
        for example:
          the translation 'error_unknown_command =
           I'm sorry, I don't know how to do '{-COMMAND-}' on {-DEVICE-}'
           contains two placeholders ({-COMMAND-} and {-DEVICE-})
           so the dictionary should have the key {-COMMAND-} with the new value as value

        :param translation_key: the key which is used to register the translation in the
        translation file
        :param variables: a dictionary of keys and values used in the text
        :returns: the translate text
        """

        log_string = 'action="method called", translation_key="{}", variables="{}"'.format(
            translation_key,
            str(variables)
        )
        self.logger(
            3,
            self.__class__.__name__,
            self.translate_text.__name__,
            log_string
        )
        start_time = time.time()

        # load initial text
        initial_text = self.translation[translation_key]

        # set the new text
        new_text = initial_text

        # now check if the varibales dictionary is set
        if variables:
            # the keys in the variables dictionary should be the same as the
            # placeholders in the translation sentence
            for key in variables:
                new_text = new_text.replace(key, variables[key])

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", new_text={}'.format(
            execution_time,
            new_text
        )
        self.logger(
            3,
            self.__class__.__name__,
            self.translate_text.__name__,
            log_string
        )

        return new_text

    def build_menu(self, buttons, n_cols):
        """
        This method builds a menu with the found buttons
        :param buttons: buttons: a list containing the buttons for telegram
        :param n_cols: n_cols: the number of columns
        :returns: a rearranged buttons group (menu)
        """
        log_string = 'action="method called", buttons={}, n_cols={}'.format(str(buttons), n_cols)
        self.logger(
            3,
            self.__class__.__name__,
            self.build_menu.__name__,
            log_string
        )
        start_time = time.time()

        menu = [buttons[i:i + n_cols] for i in range(0, len(buttons), n_cols)]

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", menu={}'.format(
            execution_time,
            menu
        )
        self.logger(
            3,
            self.__class__.__name__,
            self.build_menu.__name__,
            log_string
        )

        return menu
