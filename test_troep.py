class Bla(object):
    def handle_command_scene(self, **kwargs):
        print "Scene aangeroepen"
        print kwargs['chat_id']
        return True

    def handle_command_switch(self, **kwargs):
        print "Switch aangeroepen"
        return True

    def get(self, **kwargs):
        method = kwargs['method']
        chat_id = kwargs['chat_id']

        def func_not_found(**kwargs): # just in case we dont have the function
            print "No Function "+ method +" Found!"
            return False

        func_name = 'handle_command_' + method
        func = getattr(self, func_name, func_not_found)
        result = func(**kwargs)
        return result
        
    def some_other_method(self):
        kwargs = {'method': 'switch', 'chat_id': "123"}
        result = self.get(**kwargs)
        print result

bla = Bla()
bla.some_other_method()
