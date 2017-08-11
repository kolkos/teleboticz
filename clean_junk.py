import glob
import os

extensions = ('*.pyc', '*.log')

for ext in extensions:
    for file in glob.glob(ext):
        print "delete {}".format(file)
        os.unlink(file)
