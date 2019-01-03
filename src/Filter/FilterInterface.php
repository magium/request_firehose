<?php

namespace Magium\RequestFirehose\Filter;

interface FilterInterface
{

    public function filter(array $data);

}
