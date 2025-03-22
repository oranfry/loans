<?php

namespace Loans\Linetype;

class LoanClose extends LoanBase
{
    public function __construct()
    {
        parent::__construct();

        // rename close to date for this linetype

        unset($this->fields['close']);

        $this->fields['date'] = fn ($records): string => $records['/']->close;
        $this->unfuse_fields['close'] = fn ($line): string => $line->date;

        // negate the amount, as this is the close (a.k.a., settlement or repayment)

        $this->fields['amount'] = fn ($records): float => (float) bcsub('0', $records['/']->amount ?? '0', 2);
        $this->unfuse_fields['amount'] = fn ($line): string => bcsub('0', (string) ($line->amount ?? 0), 2);

        // description is computed

        $this->fields['description'] = function ($records): string {
            if ($records['/']->amount < 0) {
                $pieces[] = 'Repaid';

                if ($party = @$records['/']->other_party) {
                    $pieces[] = 'by ' . $party;
                }
            } else {
                $pieces[] = 'Repays';

                if ($party = @$records['/']->other_party) {
                    $pieces[] = $party;
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
            $errors[] = 'Missing date';
        }

        if (!@$line->open) {
            $errors[] = 'Missing open date';
        }

        if ($line->open >= $line->date) {
            $errors[] = 'Loan open date should be before loan close date in time';
        }

        return $errors;
    }
}
