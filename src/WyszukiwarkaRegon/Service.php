<?php

namespace WyszukiwarkaRegon;

class Service
{
    /**
     * @var string
     */
    protected $api_url = "https://wyszukiwarkaregon.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc/ajaxEndpoint/";

    /**
     * @var string
     */
    protected $pKluczUzytkownika = 'aaaaaabbbbbcccccdddd';

    /**
     * @var Transport
     */
    private $transport;

    /**
     * @var string - last known error
     */
    private $error = null;

    /**
     *
     */
    public function __construct()
    {
        $this->transport = new Transport();
        $this->transport->setBaseUrl($this->api_url);
    }

    /**
     * Set access key
     *
     * @param $key
     */
    public function setKluczUzytkownika($key)
    {
        $this->pKluczUzytkownika = $key;
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Login - get GUS session id
     *
     * @return bool|string
     */
    public function zaloguj()
    {
        $params = [
            'pKluczUzytkownika' => $this->pKluczUzytkownika
        ];

        $result = $this->transport->call('Zaloguj', 'post', $params);

        if (!isset($result['d'])) {
            return false;
        }

        if (empty($result['d'])) {
            return false;
        }

        return $result['d'];
    }

    /**
     * Get captcha image
     *
     * @param string $sid - session id returned by Zaloguj() method
     * @return bool
     */
    public function pobierzCaptcha($sid)
    {
        $headers = [
            'sid' => $sid
        ];

        $result = $this->transport->call('PobierzCaptcha', 'post', [], $headers);

        if (!isset($result['d'])) {
            return false;
        }

        if (empty($result['d'])) {
            $this->error = $this->DaneKomunikat($sid);

            return false;
        }

        return $result['d'];
    }


    /**
     * Check user entered captcha
     *
     * @param string $sid - session id returned by Zaloguj() method
     * @param string $captcha - user entered captcha
     * @return bool
     */
    public function sprawdzCaptcha($sid, $captcha)
    {
        $headers = [
            'sid' => $sid
        ];

        $params = [
            'pCaptcha' => trim($captcha)
        ];

        $result = $this->transport->call('SprawdzCaptcha', 'post', $params, $headers);

        if (!isset($result['d'])) {
            return false;
        }

        if (empty($result['d'])) {
            return false;
        }

        return $result['d'];
    }

    /**
     * @param string $sid - session id returned by Zaloguj() method
     * @param array $settings - search params array('Nip' => null, 'Regon' => null, 'Krs' => 'null'}
     * @param bool $extended - get extended information
     * @return array
     * @throws \Exception
     */
    public function daneSzukaj($sid, $settings, $extended = false, $pkd = false)
    {
        $headers = [
            'sid' => $sid
        ];

        $params = [
            'pParametryWyszukiwania' => [
                "Regon" => null,
                "Krs" => null,
                "Nip" => null
            ]
        ];

        if (isset($settings['Nip'])) {
            $params['pParametryWyszukiwania']['Nip'] = $settings['Nip'];
        }
        if (isset($settings['Regon'])) {
            $params['pParametryWyszukiwania']['Regon'] = $settings['Regon'];
        }
        if (isset($settings['Krs'])) {
            $params['pParametryWyszukiwania']['Krs'] = $settings['Krs'];
        }

        $result = $this->transport->call('daneSzukaj', 'post', $params, $headers);

        if (!isset($result['d'])) {
            return false;
        }

        if (empty($result['d'])) {
            $this->error = $this->DaneKomunikat($sid);

            return false;
        }

        $response = json_decode($result['d'], true);

        if (!$response) {
            return false;
        }

        $response = array_shift($response);

        if ($extended) {
            $eparams = [
                'pNazwaRaportu' => null,
                'pRegon' => str_pad($response['Regon'], 14, "0"),
                'pSilosID' => 1
            ];

            switch ($response['Typ']) {

                case 'F':
                    $eparams['pNazwaRaportu'] = 'DaneRaportFizycznaPubl';
                    break;

                case 'P':
                    $eparams['pNazwaRaportu'] = 'DaneRaportPrawnaPubl';
                    break;

                default:
                    throw new \Exception("Unknown type!");
            }

            $result = $this->transport->call('DanePobierzPelnyRaport', 'post', $eparams, $headers);

            if (!isset($result['d'])) {
                return false;
            }

            if (empty($result['d'])) {
                $this->error = $this->DaneKomunikat($sid);

                return false;
            }

            $data = json_decode($result['d'], true);
            $response = array_merge($response, array_shift($data));

            if ($pkd) {
                switch ($response['Typ']) {
                    case 'F':
                        $eparams['pNazwaRaportu'] = 'DaneRaportDzialalnosciFizycznejPubl';
                        break;
                    case 'P':
                        $eparams['pNazwaRaportu'] = 'DaneRaportDzialalnosciPrawnejPubl';
                        break;
                    default:
                        throw new \Exception("Unknown type!");
                }
                $result = $this->transport->call('DanePobierzPelnyRaport', 'post', $eparams, $headers);

                if (!isset($result['d'])) {
                    return false;
                }

                if (empty($result['d'])) {
                    $this->error = $this->DaneKomunikat($sid);

                    return false;
                }

                $data = json_decode($result['d'], true);

                $response = array_merge($response, ['ListaDzialalnosci' => $data]);
            }
            return $response;

        }

        return $response;

    }

    /**
     * @param string $sid - session id returned by Zaloguj() method
     * @return null|string
     */
    public function daneKomunikat($sid)
    {
        $headers = [
            'sid' => $sid
        ];

        $result = $this->transport->call('DaneKomunikat', 'post', [], $headers);

        if (!isset($result['d'])) {
            return null;
        }

        if (empty($result['d'])) {
            return null;
        }

        return $result['d'];
    }

    /**
     * @param string $sid - session id returned by Zaloguj() method
     * @return array
     */
    public function getWojewodztwa($sid)
    {
        $headers = [
            'sid' => $sid
        ];

        $result = $this->transport->call('GetWojewodztwa', 'post', [], $headers);

        if (!isset($result['d'])) {
            return null;
        }

        if (empty($result['d'])) {
            return null;
        }

        $response = json_decode($result['d'], true);

        if (!$response) {
            return false;
        }

        return $response;
    }

    /**
     * @param string $sid - session id returned by Zaloguj() method
     * @param string $param = parameter name eg. StanDanych
     * @return mixed
     */
    public function getValue($sid, $param)
    {
        $headers = [
            'sid' => $sid
        ];

        $params = [
            'pNazwaParametru' => $param
        ];

        $result = $this->transport->call('GetValue', 'post', $params, $headers);

        if (!isset($result['d'])) {
            return null;
        }

        if (empty($result['d'])) {
            return null;
        }

        return $result['d'];
    }
}
