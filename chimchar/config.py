#coding: utf-8

import os

# GLOBALs
units = ['chimchar', 'posts', 'settings', 'site', 'tpl']
settings = ['footer', 'header', 'nav', 'post', 'site']
terminal_char = '---\n'
file_type = 'mkd'

# PATHs
app_path = os.path.dirname(os.path.abspath(''))
unit_paths = {}
for i in units:
    unit_paths[i] = os.path.join(app_path, i)
setting_paths = {}
for i in settings:
    setting_paths[i] = os.path.join(unit_paths['settings'], i+'.yaml')
