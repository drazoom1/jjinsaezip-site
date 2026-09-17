import json,re
b=json.load(open('site/_b64.json'))
t=open('site/index.template.html',encoding='utf-8').read()
mime={'before':'jpeg','after':'jpeg','hero':'jpeg','wordmark':'png','favicon':'png'}
def sub(m):
    k=m.group(1); key=k.lower()
    return f'data:image/{mime.get(key,"jpeg")};base64,'+b[key]
t=re.sub(r'\{\{(\w+)\}\}',sub,t)
open('site/index.html','w',encoding='utf-8').write(t); print('built',len(t)//1024,'KB')
