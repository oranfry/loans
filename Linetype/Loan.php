<?php

namespace Loans\Linetype;

class Loan extends LoanBase
{
    public function validate($line): array
    {
        $errors = parent::validate($line);

        if (!@$line->open) {
            $errors[] = 'Missing open date';
        }

        if (!@$line->close) {
            $errors[] = 'Missing close date';
        }

        if ($line->open >= $line->close) {
            $errors[] = 'Loan open date should be before loan close date in time';
        }

        return $errors;
    }
}
