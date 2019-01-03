<?php
/**
 * Created by PhpStorm.
 * User: kschr
 * Date: 1/3/2019
 * Time: 1:36 PM
 */

namespace Magium\RequestFirehose\Adapter;


interface AdapterInterface
{

    public function publish(array $data);

}
