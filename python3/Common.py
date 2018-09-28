# coding=utf-8
#!/usr/bin/env python
import base64
import datetime
import hashlib
import hmac
import json
import urllib
import urllib.parse
import urllib.request
import requests


AccessKey = "xxxx-xxxx-xxxx-xxxxx"
SecretKey = "xxxx-xxxx-xxxx-xxxxx"
TradeUrl = "https://open-api.becent.com"



def http_get_request(url, params, add_to_headers=None):
    headers = {
        "Content-type": "application/json",
        'User-Agent': 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
    }
    if add_to_headers:
        headers.update(add_to_headers)
    postdata = urllib.parse.urlencode(params)
    response = requests.get(url, postdata, headers=headers, timeout=5)
    try:

        if response.status_code == 200:
            return response.json()
        else:
            return
    except BaseException as e:
        print("httpGet failed, detail is:%s,%s" %(response.text,e))
        return


def http_post_request(url, params, add_to_headers=None):
    headers = {
        "Accept": "application/json",
        "Content-type": "application/x-www-form-urlencoded",
        'User-Agent': 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
    }
    if add_to_headers:
        headers.update(add_to_headers)
    postdata = urllib.parse.urlencode(params).encode("utf-8")
    response = requests.post(url, postdata, headers=headers, timeout=10)
    try:

        if response.status_code == 200:
            return response.json()
        else:
            return
    except BaseException as e:
        print("httpPost failed, detail is:%s,%s" %(response.text,e))
        return


def api_key_get(params, request_path):
    method = 'GET'
    timestamp = datetime.datetime.utcnow().strftime('%Y-%m-%dT%H:%M:%S')
    params_to_sign = {'AccessKey': AccessKey,
                      'SignatureMethod': 'HmacSHA256',
                      'SignatureVersion': 'V1.0',
                      'Timestamp': timestamp}

    host_url = TradeUrl
    host_name = urllib.parse.urlparse(host_url).hostname
    host_name = host_name.lower()
    for k,v in params_to_sign.items():
        params[k] = v
    Signature = createSign(params, method, host_name, request_path, SecretKey)
    params['Signature'] = Signature
    url = host_url + request_path
    return http_get_request(url, params)


def api_key_post(params, request_path):
    method = 'POST'
    timestamp = datetime.datetime.utcnow().strftime('%Y-%m-%dT%H:%M:%S')
    params_to_sign = {'AccessKey': AccessKey,
                      'SignatureMethod': 'HmacSHA256',
                      'SignatureVersion': 'V1.0',
                      'Timestamp': timestamp}

    host_url = TradeUrl
    host_name = urllib.parse.urlparse(host_url).hostname
    host_name = host_name.lower()
    for k,v in params_to_sign.items():
        params[k] = v
    Signature = createSign(params, method, host_name, request_path, SecretKey)
    params['Signature'] = Signature
    url = host_url + request_path
    return http_post_request(url, params)


def createSign(pParams, method, host_url, request_path, secret_key):
    sorted_params = sorted(pParams.items(), key=lambda d: d[0], reverse=False)
    encode_params = urllib.parse.urlencode(sorted_params)
    payload = [method, host_url, request_path, encode_params]
    payload = '\n'.join(payload)
    payload = payload.encode(encoding='UTF8')
    secret_key = secret_key.encode(encoding='UTF8')
    digest = hmac.new(secret_key, payload, digestmod=hashlib.sha256).digest()
    signature = base64.b64encode(digest)
    signature = signature.decode()
    return signature
