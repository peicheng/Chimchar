#coding: utf-8

import web
from jinja2 import Environment, FileSystemLoader

from chimchar.db import reader
from chimchar.db import writer
from chimchar.db import post, site_setting

from config import SECRET

def template_render(template_name, path, content):
    extensions = content.pop('extensions', [])
    globals = content.pop('globals', {})

    jinja_env = Environment (
            loader = FileSystemLoader(path),
            extensions = extensions,
            )
    jinja_env.globals.update(globals)
    return jinja_env.get_template(template_name).render(content)

class before_request(object):
    def __init__(self):
        site_info = reader.get_site_setting()
        if site_info == None:
            site_info = site_setting()
            site_info.site_name = 'demo'
            site_info.site_slogan = ''
            site_info.author = 'chimchar'
            site_info.google_analytic_id = ''
            writer.set_site_setting(site_info)
        site_info.site_domain = web.ctx.host
        self.site_info = site_info

class do_auth(before_request):
    def __init__(self):
        if web.cookies().get('is_pass') != SECRET:
            web.seeother('/auth')
        before_request.__init__(self)
