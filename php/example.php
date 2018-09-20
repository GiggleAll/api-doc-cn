<?php
/**
 * Created by IntelliJ IDEA.
 */

class Demo {
	private $apiType;
	private $bcApiPath;
	private $apiAccessSecret;
	private $apiAccessKey;
	private $apiHost;
	private $httpSchemas;
	private $commonParams;

	public function __construct()
	{
		$this->apiAccessSecret = 'xxxx-xxxx-xxxx-xxxxx';
		$this->apiAccessKey = 'xxxx-xxxx-xxxx-xxxxx';
		$this->apiHost = 'open-api.becent.com';
		$this->httpSchemas = 'https';
		$this->commonParams = [
			'AccessKey'         =>$this->apiAccessKey,
			'SignatureMethod'   =>'HmacSHA256',
			'SignatureVersion'  =>'V1.0',
			'Timestamp'         =>date('Y-m-d\TH:i:s', time()),
		];
	}

	//生成签名
	private function genSign($params) {
		$tempArr = [];
		foreach ($params as $key => $value) {
			$tempArr[] = $key . '=' . urlencode($value);
		}
		asort($tempArr);
		$paramsStr = implode('&', $tempArr);
		$signParam1 = $this->apiType."\n".$this->apiHost."\n".$this->bcApiPath."\n".$paramsStr;
		$sigNature = hash_hmac('sha256', $signParam1, $this->apiAccessSecret, true);
		$sigNature = base64_encode($sigNature);
		return $sigNature;
	}


	//POST请求示例:
	public function createBuyOrder() {
		$this->bcApiPath = '/v1/exg/sell';
		$this->apiType = 'POST';
		$params = $this->commonParams;
		$params['symbol'] = 'ETH/USDT';
		$params['tokenNum'] = 2.45;
		$params['sprice'] = 478.2;

		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}";
		$httpResStr = $this->httpCurl($httpUrl,$params);

		return json_decode($httpResStr, true);
	}

	public function cancelOrder() {
		$this->bcApiPath = '/v1/exg/cancel';
		$this->apiType = 'POST';
		$params = $this->commonParams;
		$params['orderId'] = '111111';

		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}";
		$httpResStr = $this->httpCurl($httpUrl,$params);

		return json_decode($httpResStr, true);
	}


	//get请求示例:
	public function getMarketDepthFromBecent() {
		$this->bcApiPath = '/v1/market/depth';
		$this->apiType = 'GET';
		//验证参数
		$params = $this->commonParams;
		$params['symbol'] = "ETH/USDT";

		//创建签名
		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$tempArr = [];
		foreach ($params as $key => $value) {
			$tempArr[] = $key . '=' . urlencode($value);
		}
		$paramsStr = implode('&', $tempArr);
		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}?{$paramsStr}";
		$httpResStr = $this->httpCurl($httpUrl);
		return json_decode($httpResStr, true);
	}


	public function getMarketExgPairs() {
		$this->bcApiPath = '/v1/market/allpairs';
		$this->apiType = 'GET';
		//验证参数
		$params = $this->commonParams;

		//创建签名
		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$tempArr = [];
		foreach ($params as $key => $value) {
			$tempArr[] = $key . '=' . urlencode($value);
		}
		$paramsStr = implode('&', $tempArr);
		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}?{$paramsStr}";
		$httpResStr = $this->httpCurl($httpUrl);
		return json_decode($httpResStr, true);
	}

	public function getEntrustOrders() {
		$this->bcApiPath = '/v1/exg/orders/hisotory';
		$this->apiType = 'GET';
		//验证参数
		$params = $this->commonParams;
		$params['symbol'] = "BTC/USDT";
		$params['status'] = 'submitting,partial-filled';

		//创建签名
		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$tempArr = [];
		foreach ($params as $key => $value) {
			$tempArr[] = $key . '=' . urlencode($value);
		}
		$paramsStr = implode('&', $tempArr);
		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}?{$paramsStr}";
		$httpResStr = $this->httpCurl($httpUrl);
		return json_decode($httpResStr, true);
	}


	public function getAccount() {
		$this->bcApiPath = '/v1/account/accounts';
		$this->apiType = 'GET';
		//验证参数
		$params = $this->commonParams;
		$params['symbol'] = "ETH/USDT";

		//创建签名
		$sign = $this->genSign($params);
		$params['Signature'] = $sign;

		$tempArr = [];
		foreach ($params as $key => $value) {
			$tempArr[] = $key . '=' . urlencode($value);
		}
		$paramsStr = implode('&', $tempArr);
		$httpUrl = "{$this->httpSchemas}://{$this->apiHost}{$this->bcApiPath}?{$paramsStr}";
		$httpResStr = $this->httpCurl($httpUrl);
		return json_decode($httpResStr, true);
	}


	private function httpCurl($url,$postdata=[], $proxy=['proxyHost','proxyPort']) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		if ( !empty($postdata) ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			$tempArr = [];
			foreach ($postdata as $key =>$val) {
				$tempArr[] = $key . '=' . urlencode($val);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $tempArr));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HEADER,0);
		if ( !empty($proxy) ) {
			curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
			curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_HTTPHEADER, [
			"Content-Type: application/x-www-form-urlencoded",
			"cache-control: no-cache",
		]);
		$output = curl_exec($ch);

		$info = curl_getinfo($ch);
		curl_close($ch);
		return $output;
	}
}


$obj = new Demo();
$res = $obj->getEntrustOrders();
print_r($res);


