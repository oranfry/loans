<?php

namespace Loans\Linetype;

class LoanOpen extends LoanBase
{
    public function __construct()
    {
        parent::__construct();

        // rename open to date for this linetype

        unset($this->fields['open']);

        $this->fields['date'] = fn ($records): string => $records['/']->open;
        $this->unfuse_fields['open'] = fn ($line): string => $line->date;

        // description is computed

        $this->fields['description'] = function ($records): string {
            if ($records['/']->amount > 0) {
                $pieces[] = 'Borrowed';

                if ($party = @$records['/']->other_party) {
                    $pieces[] = 'from ' . $party;
                }
            } else {
                $pieces[] = 'Lent';

                if ($party = @$records['/']->other_party) {
                    $pieces[] = 'to ' . $party;
                }
            }

            if ($reason = @$records['/']->reason) {
                $pieces[] = 'for ' . $reason;
            }

            return implode(' ', $pieces);
        };
    }

    public function validate($line): array
    {
        $errors = parent::validate($line);

        if (!@$line->date) {
            $errors[] = 'no date';
        }

        if (!@$line->close) {
            $errors[] = 'no close date';
        }

        if ($line->date >= $line->close) {
            $errors[] = 'Loan open date should be before loan close date in time';
        }

        return $errors;
    }
}
