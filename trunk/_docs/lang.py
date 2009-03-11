#!/usr/bin/python

import glob, os, re, xpath
from xml.dom import minidom

def dirwalk(dir):
    "walk a directory tree, using a generator"
    for f in os.listdir(dir):
        fullpath = os.path.join(dir,f)
        if os.path.isdir(fullpath) and not os.path.islink(fullpath):
            for x in dirwalk(fullpath):  # recurse into subdir
                yield x
        else:
            yield fullpath

jtext = re.compile('(JText::(_|sprintf)\\( *\'(([^\']|(\\\\\'))*)\' *\\))')
xpathstring = '|'.join(['//param/@label', 
	'/param/@description',
	'/param/option//text()',
	'//metadata/(view|layout)/@title',
	'//metadata/(view|layout)/message//text()',
	'//metadata/state/(name|description)/text()',
	])

for component in glob.glob('../com_*'):
	for site in ('site', 'admin'):
		strings, oldstrings = {}, {}
		langfile = os.path.join(component, site, 'languages', 'en-GB.'+component[3:]+'.ini')
		if os.path.exists(langfile):
			for line in open(langfile).readlines():
				trans = line.split('=', 1)
				if len(trans) <= 1:
					continue
				oldstrings[trans[0].strip().upper()] = trans[1].strip()
		for path in dirwalk(os.path.join(component, site)):
			if path[-4:] == '.php':
				for stringgr in jtext.findall(open(path).read()):
					string = stringgr[2].strip()
					if oldstrings.has_key(string.upper()):
						strings[string.upper()] = oldstrings[string.upper()]
					else:
						strings[string.upper()] = string
			elif path[-4:] == '.xml':
				dom = minidom.parse(path)
				for string in xpath.findvalues(xpathstring, dom):
					string = string.strip()
					if string == '':
						continue
					if oldstrings.has_key(string.upper()):
						strings[string.upper()] = oldstrings[string.upper()]
					else:
						strings[string.upper()] = string
					
				 
		f = open(langfile+'.new', 'w')
		for key in sorted(strings):
			f.write(key+'='+strings[key]+"\n")
		f.write("\n#not found\n")
		for key in sorted(oldstrings):
			if not strings.has_key(key):
				f.write(key+'='+oldstrings[key]+"\n")

		
				
	
	
