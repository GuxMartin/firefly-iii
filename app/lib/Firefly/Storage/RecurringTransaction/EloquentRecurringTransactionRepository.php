<?php


namespace Firefly\Storage\RecurringTransaction;

use Carbon\Carbon;

/**
 * Class EloquentRecurringTransactionRepository
 *
 * @package Firefly\Storage\RecurringTransaction
 */
class EloquentRecurringTransactionRepository implements RecurringTransactionRepositoryInterface
{

    /**
     * @param \User $user
     * @return mixed|void
     */
    public function overruleUser(\User $user)
    {
        $this->_user = $user;
        return true;
    }

    protected $_user = null;

    /**
     *
     */
    public function __construct()
    {
        $this->_user = \Auth::user();
    }

    /**
     * @param \RecurringTransaction $recurringTransaction
     *
     * @return bool|mixed
     */
    public function destroy(\RecurringTransaction $recurringTransaction)
    {
        $recurringTransaction->delete();

        return true;
    }

    public function findByName($name)
    {
        return $this->_user->recurringtransactions()->where('name', 'LIKE', '%' . $name . '%')->first();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->_user->recurringtransactions()->get();
    }

    /**
     * @param $data
     *
     * @return mixed|\RecurringTransaction
     */
    public function store($data)
    {
        $recurringTransaction = new \RecurringTransaction;
        $recurringTransaction->user()->associate($this->_user);
        $recurringTransaction->name       = $data['name'];
        $recurringTransaction->match      = join(' ', explode(',', $data['match']));
        $recurringTransaction->amount_max = floatval($data['amount_max']);
        $recurringTransaction->amount_min = floatval($data['amount_min']);

        // both amounts zero:
        if ($recurringTransaction->amount_max == 0 && $recurringTransaction->amount_min == 0) {
            $recurringTransaction->errors()->add('amount_max', 'Amount max and min cannot both be zero.');

            return $recurringTransaction;
        }

        $recurringTransaction->date        = new Carbon($data['date']);
        $recurringTransaction->active      = isset($data['active']) ? intval($data['active']) : 0;
        $recurringTransaction->automatch   = isset($data['automatch']) ? intval($data['automatch']) : 0;
        $recurringTransaction->skip        = isset($data['skip']) ? intval($data['skip']) : 0;
        $recurringTransaction->repeat_freq = $data['repeat_freq'];

        if ($recurringTransaction->validate()) {
            $recurringTransaction->save();
        }

        return $recurringTransaction;
    }

    /**
     * @param \RecurringTransaction $recurringTransaction
     * @param                       $data
     *
     * @return mixed|void
     */
    public function update(\RecurringTransaction $recurringTransaction, $data)
    {
        $recurringTransaction->name       = $data['name'];
        $recurringTransaction->match      = join(' ', explode(',', $data['match']));
        $recurringTransaction->amount_max = floatval($data['amount_max']);
        $recurringTransaction->amount_min = floatval($data['amount_min']);

        // both amounts zero:
        if ($recurringTransaction->amount_max == 0 && $recurringTransaction->amount_min == 0) {
            $recurringTransaction->errors()->add('amount_max', 'Amount max and min cannot both be zero.');

            return $recurringTransaction;
        }
        $recurringTransaction->date        = new Carbon($data['date']);
        $recurringTransaction->active      = isset($data['active']) ? intval($data['active']) : 0;
        $recurringTransaction->automatch   = isset($data['automatch']) ? intval($data['automatch']) : 0;
        $recurringTransaction->skip        = isset($data['skip']) ? intval($data['skip']) : 0;
        $recurringTransaction->repeat_freq = $data['repeat_freq'];

        if ($recurringTransaction->validate()) {
            $recurringTransaction->save();
        }

        return $recurringTransaction;

    }

}