#coding: utf-8

import codecs
from jinja2 import Environment, FileSystemLoader
import markdown
import os
import time
import yaml

base_loader = yaml.load
base_render = markdown.markdown

app_path = os.path.dirname(os.path.abspath(''))
settings_file = os.path.join(app_path, 'settings.yaml')

terminal_char = '---\n'

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

class loader(object):
    # Load every thing
    def __init__(self):
        return

    def load(self, fn):
        lines = codecs.open(fn, encoding = 'utf-8').readlines()
        return lines

    def parse(self, l, sp, ep, render):
        p = ''
        for i in range(sp, ep):
            p += l[i]
        return render(p)

class post(loader):
    # Load a post
    def __init__(self, fn, settings, default_settings):
        loader.__init__(self)
        self.fn = fn
        self.settings = settings
        self.default = default_settings
        self.lines = self.load(self.fn)
        self.parse(self.lines)

    def parse(self, l):
        try:
            default = self.default
            til = l.index(terminal_char)
            self.options = loader.parse(self, l, 0, til, base_loader)
            self.content = loader.parse(self, l, til+1, len(l), base_render)
            for key, values in default.items():
                if not self.options.has_key(key):
                    self.options[key] = values
            self.options['time'] = '' #TODO mt & ct
        except ValueError:
            #TODO handler the leak of terminal_char
            return ''

        #all in pack
        self.pack = {'content': self.content}
        for key in self.options.keys():
            self.pack[key] = self.options[key]
        return self.pack

class setting(loader):
    # Load settings
    def __init__(self, fn):
        loader.__init__(self)
        self.fn = fn
        self.lines = self.load(fn)
        self.parse(self.lines)

    def parse(self, l):
        try:
            til = l.index(terminal_char)
            self.options = loader.parse(self, l, 0, til, base_loader)
        except ValueError:
            self.options = ''
        return self.options

class worker(object):
    def __init__(self, fn = settings_file):
        self.fn = fn
        self.settings = self.get_settings()
        self.paths = self.get_paths()
        self.posts = self.get_posts()
        self.nav = self.get_nav()
        return

    def get_settings(self):
        return setting(self.fn).options

    def get_paths(self):
        paths = {}
        stuff = self.settings['unit']

        for i in stuff:
            paths[i] = os.path.join(app_path, i)
        return paths

    def get_posts(self):
        l = []
        path = self.paths['posts']
        ftype = self.settings['globals']['file_type']
        default = self.settings['post']
        settings = self.settings['globals']
        time = os.path.getmtime
        join = os.path.join

        for f in os.listdir(path):
            if f[len(f)-len(ftype):len(f)] == ftype:
                l.append((time(join(path, f)), post(join(path, f), settings, default).pack))
        l.sort()
        l.reverse()
        return [j for (i, j) in l]

    def get_nav(self):
        l = []
        pre_define = self.settings['nav']
        plist = self.posts

        for key, values in pre_define.items():
            l.append((key, values))

        for i in plist:
            if i['is_page'] == 1:
                l.append((i['title'], i['url']))
        return l

    def rende(self):
        self.rende_posts()
        self.rende_index()
        self.rende_feed()

    def rende_posts(self, render = jinja2_render, writer = file_writer):
        tpl = self.paths['tpl']
        output = self.paths['site']
        template_values = {
                'header': self.settings['header'],
                'nav': self.nav,
                'site': self.settings['site']
                }
        plist = self.posts

        for i in plist:
            template_values['post'] = i
            output_name = i['url'] + '.html'
            formatted = render('posts.html', tpl, template_values)
            writer(output_name, output, formatted)
        return

    def rende_index(self, render = jinja2_render, writer = file_writer):
        #TODO better index render
        tpl = self.paths['tpl']
        output = self.paths['site']
        template_values = {
                'header': self.settings['header'],
                'nav': self.nav,
                'site': self.settings['site'],
                'posts': self.posts
                }
        output_name = 'index.html'
        formatted = render('index.html', tpl, template_values)
        writer(output_name, output, formatted)
        return

    def rende_feed(self, render = jinja2_render, writer = file_writer):
        tpl = self.paths['tpl']
        output = self.paths['site']
        template_values = {
                'site': self.settings['site'],
                'posts': self.posts
                }
        template_values['site']['site_updated'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        output_name = 'feed.xml'
        formatted = render('feed.xml', tpl, template_values)
        writer(output_name, output, formatted)

if __name__ == '__main__':
    worker().rende()
