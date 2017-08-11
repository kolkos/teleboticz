import glob
import os

extensions = ('*.pyc', '*.log')

for ext in extensions:
    for file in glob.glob(ext):
        os.unlink(file)
