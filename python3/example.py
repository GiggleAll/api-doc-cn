#!/usr/bin/env python

from Common import *

# The following provides examples of GET and POST requests. Because the workload is too large, other interfaces should be asked by developers.

#POST
def create_buyOrder(symbol, tokenNum, sprice):
    """
    :param symbol: 交易对,可选值{ETH/USDT, BTC/USDT}
    :param tokenNum : 委托购买数量
    :param sprice : 委托购买价格
    :return:
    """
    path = '/v1/exg/buy'
    params = {
        'symbol': symbol,
        'tokenNum': tokenNum,
        'sprice': sprice,
    }
    return api_key_post(params, path)


# res = create_buyOrder('ETH/USDT', 1.2, 210.2)
# print(res)


#GET
def get_accounts():
    """
    :param symbol: 交易对, eg:ETH/USDT
    :return:
    """
    path = '/v1/account/accounts'
    params = {
    }

    return api_key_get(params, path)


res = get_accounts()
print(res)



