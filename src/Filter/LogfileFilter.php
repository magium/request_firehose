<?php

namespace Magium\RequestFirehose\Filter;

class LogfileFilter implements FilterInterface
{

    public function filter(array $data)
    {
        $date = new \DateTime();
        $parts = [];
        $parts[] = $date->format(\DateTime::ISO8601);
        $parts[] = $this->formatIpAddress($data['server']??[]);
        $parts[] = $data['server']['REQUEST_METHOD']??'';
        $parts[] = $data['server']['REQUEST_URI']??'';
        $parts[] = $data['response_code']??200;
        $parts[] = sprintf('"%s"', $data['server']['HTTP_USER_AGENT']??'');
        return implode(' ', $parts);
    }

    public function formatIpAddress($server)
    {
        $ip = !empty($server['HTTP_X_FORWARDED_FOR']) ? $server['HTTP_X_FORWARDED_FOR'] . ' - ' : '';
        $ip .= $server['REMOTE_ADDR']??'';
        return $ip;
    }

}
