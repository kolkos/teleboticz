"""
File:                 Domoticz.py

This file declares the methods which are used to communicate with Domoticz

class:
    Domoticz -- responsible for getting and sending information from/to
                domoticz

    methods:
        __init__
            Initiate this class.
            the variable self.general is used to call the General class

        domoticz_api_call
            General method to send to and receive info from the api

        get_domoticz_scenes
            this method is called to get the scenes from domoticz. This
            method uses some items from the configuration file:
            EXCLUDES -> scenes => To exclude scenes from the results

        get_domoticz_switches
            more or less the same method as the method above, with the
            exception that the switches api call needs somewhat more information

        create_status_message
            method to receive information from domoticz and create an
            status message. This message will be send to the telegram client


---- Changelog ----

Version:              0.1
Date:                 01-07-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import re
import time
from General import General
from Database import Database
import requests

class Domoticz(object):
    """
    Domoticz methods
    """
    def __init__(self):
        self.general = General()
        self.database = Database()

        self.domoticz_url = None
        self.create_domoticz_url()
        self.domoticz_results = {}
        self.domoticz_excludes = {}
        self.domoticz_call_config = {}
        self.get_excludes()


    def create_domoticz_url(self):
        """
        method to generate the domoticz url
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.create_domoticz_url.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        url = "http://"

        # check if the username is set
        if len(self.general.config.get("Domoticz", "user")) > 0:
            # the username is set, append the username and password to the url
            url += self.general.config.get("Domoticz", "user") + ":"
            url += self.general.config.get("Domoticz", "pass") + "@"

        # add the hostname to the url
        url += self.general.config.get("Domoticz", "hostname")

        # add the port to the url
        url += ":" + self.general.config.get("Domoticz", "port")

        # finally append the json file to the url
        url += '/json.htm'
        self.domoticz_url = url

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", url="{}"'.format(
            execution_time,
            url
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.create_domoticz_url.__name__,
            log_string
        )

    def get_excludes(self):
        """
        This method gets all the excluded devices from the database. The result
        is a Dictionary with lists of excluded devices.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_excludes.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        query = "SELECT type, idx FROM domoticz_excluded_items ORDER BY type ASC"
        results = self.database.select_handler(query)
        # loop through the results
        for row in results:
            device_type = row[0]
            idx = row[1]

            # check if the device_type already exists, if not create the list
            if not device_type in self.domoticz_excludes:
                self.domoticz_excludes[device_type] = []

            self.domoticz_excludes[device_type].append(idx)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_excludes.__name__,
            log_string
        )


    def domoticz_api_call(self, parameters):
        """
        General function to send or request info to/from domoticz api
        :param parameters: the parameters for the api call
        :returns: data, the result from the api call (None if false)
        """
        log_string = 'action="method called", parameters="{}"'.format(str(parameters))
        self.general.logger(
            3,
            self.__class__.__name__,
            self.domoticz_api_call.__name__,
            log_string)
        start_time = time.time()

        try:
            resp = requests.get(url=self.domoticz_url, params=parameters)
            data = resp.json()
            self.general.logger(
                2,
                self.__class__.__name__,
                self.domoticz_api_call.__name__,
                'result="call successfull"')
        except requests.exceptions.RequestException as error:
            # e = sys.exc_info()[0]
            print error
            log_string = 'result="call failed", error={}'.format(str(error))
            self.general.logger(
                1,
                self.__class__.__name__,
                self.domoticz_api_call.__name__,
                log_string)
            data = None

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.domoticz_api_call.__name__,
            log_string
        )

        return data

    def get_domoticz_call_config(self):
        """
        This method gets the configuration for the domoticz calls from the database.
        This method uses the table domoticz_call_config in the database as a configuration for the
        call. In this table is described wich parameters the api call needs, which fields are import
        in the response and if the device is changable.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_domoticz_call_config.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # load the configuration from the database
        query = "SELECT device_type, section, key, value FROM domoticz_call_config"
        results = self.database.select_handler(query)

        # loop through the results and append them to the global dictionary
        for row in results:
            device_type = row[0]
            section = row[1]
            key = row[2]
            value = row[3]

            # check if the device_type already exists in the global config dictionary
            # if not create this item
            if device_type not in self.domoticz_call_config:
                self.domoticz_call_config[device_type] = {}
            
            # check if the section already exists in the global dictionary
            if section not in self.domoticz_call_config[device_type]:
                # section does not exist, create it
                self.domoticz_call_config[device_type][section] = {}

            # now add the key/values to this section
            self.domoticz_call_config[device_type][section][key] = value

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_domoticz_call_config.__name__,
            log_string
        )
        return

    def get_domoticz_info(self):
        """
        Method to get the required information from Domoticz. 
        Results of this call will be saved in a global variable.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_domoticz_info.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # get the config from the database
        self.get_domoticz_call_config()

        # first loop through the device types
        for device_type in self.domoticz_call_config:
            log_string = 'action="Preparing call", device_type="{}"'.format(device_type)
            self.general.logger(
                2,
                self.__class__.__name__,
                self.get_domoticz_info.__name__,
                log_string
            )

            # check if api_call is declared
            if 'api_call' not in self.domoticz_call_config[device_type]:
                # api call parameters not available
                # skip this device type
                log_string = 'error_message="api_call not available in {}"'.format(device_type)
                self.general.logger(
                    1,
                    self.__class__.__name__,
                    self.get_domoticz_info.__name__,
                    log_string
                )
                continue

            # check if the respons section is set
            if 'respons' not in self.domoticz_call_config[device_type]:
                # api call parameters not available
                # skip this device type
                log_string = 'error_message="response not available in {}"'.format(device_type)
                self.general.logger(
                    1,
                    self.__class__.__name__,
                    self.get_domoticz_info.__name__,
                    log_string
                )
                continue

            # now it is time to do the api call
            # create the params variable for the call
            params = self.domoticz_call_config[device_type]['api_call']
            # now doe the api call
            data = self.domoticz_api_call(params)

            # check if the results are not empty
            if data is None:
                log_string = 'error_message="no valid results for {}"'.format(device_type)
                self.general.logger(
                    1,
                    self.__class__.__name__,
                    self.get_domoticz_info.__name__,
                    log_string
                )
                continue

            # loop through the results of the call (data)
            for i, dummy in enumerate(data["result"]):
                # every i is a result

                # each device will be registered with there idx
                # therefore we need to check if the idx is defined in the config
                if 'idx' not in self.domoticz_call_config[device_type]['respons']:
                    log_string = 'error_message="idx not found in {}"'.format(
                        device_type
                    )
                    self.general.logger(
                        1,
                        self.__class__.__name__,
                        self.get_domoticz_info.__name__,
                        log_string
                    )
                    continue

                # each results consists out of key and value pairs
                # the needed key/values are defined in the config, therefore
                # we need to loop throug the respons section of the config
                for result_field in self.domoticz_call_config[device_type]['respons']:
                    # result_field is the name of the field in the results dictionary
                    # location is the location of the field in the domoticz (JSON) respons
                    location = self.domoticz_call_config[device_type]['respons'][result_field]

                    # now add the results to the global results dictionary
                    # check if the device_type already exists, if not create it
                    if device_type not in self.domoticz_results:
                        self.domoticz_results[device_type] = {}

                    # get the idx of the device
                    idx = data["result"][i]["idx"]

                    # check if the idx is on the excludes list
                    try:
                        # use a try, because the device type doesn't have to be on the excludes list
                        if int(idx) in self.domoticz_excludes[device_type]:
                            continue
                    except KeyError:
                        pass

                    # check if the idx is already registered
                    if idx not in self.domoticz_results[device_type]:
                        self.domoticz_results[device_type][idx] = {}

                    # now add the other fields
                    # check if the location actually exists in the resons
                    if location not in data["result"][i]:
                        log_string = 'error_message="{} not found in result {} of {}"'.format(
                            location,
                            i,
                            device_type
                        )
                        self.general.logger(
                            1,
                            self.__class__.__name__,
                            self.get_domoticz_info.__name__,
                            log_string
                        )
                        continue

                    value = data["result"][i][location]
                    self.domoticz_results[device_type][idx][result_field] = value
            
            log_string = 'action="Call finished", device_type="{}"'.format(device_type)
            self.general.logger(
                2,
                self.__class__.__name__,
                self.get_domoticz_info.__name__,
                log_string
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.get_domoticz_info.__name__,
            log_string
        )

        return

    def create_status_message(self):
        """
        This method creates the status message. Each device type in the results dictionary
        will be split into a line. The domoticz_call_config contains all the information needed
        to create the overview. The section status_msg and icon are used.

        If the key field in the status_msg contains brackets ({}) then this field will be displayed
        as well. Field names without brackets are ignored.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.create_status_message.__name__,
            'action="Method called"'
        )
        start_time = time.time()

        # get the last known status
        self.get_domoticz_info()

        message = ""

        # loop through the results
        for device_type in self.domoticz_results:
            # check if the icon is set, if so get the icon
            icon = ""
            if 'icon' in self.domoticz_call_config[device_type]:
                icon = self.domoticz_call_config[device_type]['icon']['icon']
            else:
                icon = ":question:"

            # now loop through to the devices in the result
            for idx in self.domoticz_results[device_type]:
                line = icon
                # first get the name
                try:
                    name = self.domoticz_results[device_type][idx][
                        self.domoticz_call_config[device_type]['status_msg']['name']
                    ]
                except KeyError as error:
                    log_string = 'error_message="can\'t find the name field", error="{}"'.format(
                        str(error)
                    )
                    self.general.logger(
                        1,
                        self.__class__.__name__,
                        self.create_status_message.__name__,
                        log_string
                    )
                    continue
                line += " " + name + ":"

                # get the required fields for this device_type
                # these fields are listed in the config
                for key in self.domoticz_call_config[device_type]['status_msg']:
                    # skip the name
                    if key == 'name':
                        continue

                    # get the location
                    location = self.domoticz_call_config[device_type]['status_msg'][key]
                    # check if the key needs to be displayed (the key is between brackets)
                    check_brackets = re.match(r'^({.*})$', key)
                    try:
                        if check_brackets:
                            line += " " + key + ": " + str(self.domoticz_results[device_type][idx][location])
                        else:
                            line += " " + self.domoticz_results[device_type][idx][location]
                    except KeyError:
                        log_string = 'error_message="can\'t find {} in {}""'.format(
                            location,
                            idx
                        )
                        self.general.logger(
                            1,
                            self.__class__.__name__,
                            self.create_status_message.__name__,
                            log_string
                        )
                        continue

                line += "\n"
                message += line

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.create_status_message.__name__,
            log_string
        )

        return message

    def get_single_status(self, device_type, idx):
        """
        Method to get the status of a single device
        """
        # first wait 
        time.sleep(1)

        # update all devices
        self.get_domoticz_info()

        # now get the status
        status = self.domoticz_results[device_type][idx]['status']

        return status
