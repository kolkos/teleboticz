"""
INSERT INTO excluded_domoticz_items (type, idx, description) 
VALUES
('switch', 15, 'Werkkamer apparatuur'),
('switch', 51, 'Status PC (Virtueel)'),
('scene', 2, 'Niet thuis - apparatuur'),
('scene', 4, '	Thuis - apparatuur');



"""

from PythonClasses.Domoticz import Domoticz
import json


domoticz = Domoticz()

#domoticz.get_domoticz_info()


#print json.dumps(domoticz.domoticz_call_config, sort_keys=True, indent=4)



message = domoticz.create_status_message()
print json.dumps(domoticz.domoticz_results, sort_keys=True, indent=4)
print message

