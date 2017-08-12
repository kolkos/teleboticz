import glob
import os

extensions = ('*.pyc', '*.log', '*.pid')

for ext in extensions:
    for file in glob.glob(ext):
        print "delete {}".format(file)
        os.unlink(file)

/home/pi/python/rpi_backlight/backlight_listener.py