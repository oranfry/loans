<?php

namespace Loans\Linetype;

use simplefields\traits\SimpleFields;

abstract class LoanBase extends \jars\Linetype
{
    use SimpleFields;

    public function __construct()
    {
        $this->table = 'loan';

        $this->simple_date('open');
        $this->simple_date('close');
        $this->simple_string('other_party');
        $this->simple_string('reason');
        $this->simple_float('amount', 2);
        $this->simple_literal('account', 'loan');
    }

    public function validate($line): array
    {
        $errors = parent::validate($line);

        if ((float) @$line->amount === 0) {
            $errors[] = 'Missing amount';
        }

        return $errors;
    }
}
