#coding: utf-8

import yaml
import markdown
import codecs
from jinja2 import Environment, FileSystemLoader

from config import *

# collect things
# collect one time just enough.
def get_settings(slist = setting_paths):
    s = {}
    for key,values in slist.items():
        s[key] = setting(values).options
    return s

def get_posts(path = unit_paths['posts'], ftype = file_type):
    l = []
    for f in os.listdir(path):
        if f[len(f)-len(ftype):len(f)] == ftype:
            l.append(post(os.path.join(unit_paths['posts'], f)).pack)
    return l

def get_nav(plist):
    s = []
    for i in plist:
        if i['is_page'] == 1:
            s.append(i)
    return s

def file_writer(fn, path, content):
    f = open(os.path.join(path, fn), 'w')
    f.write(content.encode('utf-8'))
    f.close()

    return

def jinja2_render(template_name, path, content):
    extensions = content.pop('extensions', [])
    globals = content.pop('globals', {})

    jinja_env = Environment (
            loader = FileSystemLoader(path),
            extensions = extensions,
            )
    jinja_env.globals.update(globals)
    return jinja_env.get_template(template_name).render(content)

# worker
# TODO feed render

class worker(object):
    def __init__(self):
        return

    def load(self, fn = ''):
        if fn:
            lines = codecs.open(fn, encoding='utf-8').readlines()
            return lines
        else:
            return False

    def parse(self, l, sp, ep, render):
        p = ''
        for i in range(sp, ep):
            p += l[i]
        return render(p)

class post(worker):
    def __init__(self, fn, default_options = ''):
        worker.__init__(self)

        if not default_options:
            self.get_default_options()

        self.fn = fn
        self.lines = self.load(fn)
        self.parse(self.lines)

    def get_default_options(self):
        parser = worker().parse
        l = worker().load(setting_paths['post'])
        ep = l.index(terminal_char)
        self.default_options = worker.parse(self, l, 0, ep, yaml.load)
        return self.default_options

    def parse(self, l):
        try:
            til = l.index(terminal_char)
            
            # post options
            self.options = worker.parse(self, l, 0, til, yaml.load)
            for key, items in self.default_options.items():
                #fill in the default_options if it is blank
                if not self.options.has_key(key):
                    self.options[key] = items
            self.options['time'] = '' #TODO modified time & created time

            # post content
            self.content = worker.parse(self, l, til+1, len(l), markdown.markdown)
        except ValueError:
            #TODO handle the leak of terminal_char
            return ''

        # combine the options and the content
        self.pack = {'content': self.content}
        for key in self.options.keys():
            self.pack[key] = self.options[key]

        return self.pack

class setting(worker):
    def __init__(self, fn):
        worker.__init__(self)
        self.fn = fn
        self.lines = self.load(fn)
        self.parse(self.lines)

    def parse(self, l):
        try:
            til = l.index(terminal_char)
            self.options = worker.parse(self, l, 0, til, yaml.load)
        except ValueError:
            self.options = ''

        return self.options

class posts(object):
    def __init__(self, template_values):
        self.template_values = template_values

        return

    def rende(self, flist, output = unit_paths['site'], render = jinja2_render, writer = file_writer):
        for i in flist:
            template_values = self.template_values
            template_values['post'] = i

            output_name = i['url'] + '.html'
            formatted = render('posts.html', unit_paths['tpl'], template_values)
            writer(output_name, output, formatted)
        return

class index(object):
    def __init__(self, template_values):
        self.template_values = template_values
        
        return

    def rende(self, flist, output = unit_paths['site'], render = jinja2_render, writer = file_writer):
        template_values = self.template_values
        template_values['posts'] = flist
        
        output_name = 'index.html'
        formatted = render('index.html', unit_paths['tpl'], template_values)
        writer(output_name, output, formatted)

        return

if __name__ == '__main__':
    s = get_settings()
    p = get_posts()
    w = posts(s)
    w.rende(p)
