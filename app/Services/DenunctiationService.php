<?php

namespace App\Services;

use App\Exceptions\BalanceMismatchException;
use Illuminate\Http\Request;
use App\Repositories\Denunciations;
use App\Services\ApplicationService;
use App\Models\Balances\Division as BalanceDivision;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class DenunciationService extends ApplicationService
{
    protected $denunciationRepository;

    public function __construct()
    {
        $this->denunciationRepository = new DenunciationRepository;
    }

    public function balances(Request $request)
    {
        $balances = $this->balanceRepository->filterByParams($request->all());
        return $balances;
    }

    public function create($request)
    {

        $balance = new Balance();
        $balance->year = $request["year"];
        $balance->initial_balance = $this->convertToFloat($request['initial_balance']);
        $balance->current_balance = $this->convertToFloat($request['initial_balance']);
        $balance->save();

        return $balance;
    }

    public function update(Balance $balance, $request)
    {
        // Example logic for updating a photo record
        try {
            if ($balance->initial_balance != $balance-> current_balance) {
                throw new BalanceMismatchException();
            }
            $balance->initial_balance = $this->convertToFloat($request['initial_balance']);
            $balance->current_balance = $this->convertToFloat($request['initial_balance']);
            $balance->state = $request['state'];
            $balance->save();

            return $balance;
        } catch (BalanceMismatchException $e) {
            return back()->withErrors($e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Something went wrong.');
        }
    }

    public function delete(Balance $balance)
    {
        // Example logic for deleting a photo record
        if ($balance->state == "active"){
            $balance->state = "archived";
        }else{
            $balance->state = "active";
        }
        $balance->save();

        return $balance;
    }

    public function divisions(Balance $balance){
        $balance_divisions = $balance->balance_divisions();
        return $balance_divisions;
    }

    public function create_division(Balance $balance, $request){
        try {
            DB::statement('LOCK TABLES balances WRITE, balance_divisions WRITE');

            $remain_balance = $balance->current_balance - $this->convertToFloat($request["initial_balance"]);

            if ($remain_balance < 0)
            {
                throw new BalanceMismatchException("Unable to add new division because remain balance is not enough.");
            }

            $division = new BalanceDivision();
            $division->division_id = $request["division_id"];
            $division->balance_id = $balance->id;
            $division->current_balance = $this->convertToFloat($request["initial_balance"]);
            $division->initial_balance = $this->convertToFloat($request["initial_balance"]);
            $division->in_progress_balance = 0;
            $division->save();

            $balance->current_balance = $remain_balance;
            $balance->save();

            DB::statement('UNLOCK TABLES');

            return $division;
        } catch (BalanceMismatchException $e) {
            DB::statement('UNLOCK TABLES');
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            DB::statement('UNLOCK TABLES');
            throw new Exception($e->getMessage());
        }

    }

    public function update_division(Balance $balance, $request)
    {
        try {
            // Lock the necessary tables
            DB::statement('LOCK TABLES balances WRITE, balance_divisions WRITE, sessions WRITE');

            // Fetch the balance division record
            $balance_division = BalanceDivision::where('id', '=', $request["id"])
                ->where('balance_id', '=', $balance->id)
                ->where('division_id', '=', $request["division_id"])
                ->firstOrFail();

            if ($balance_division->initial_balance != $balance_division->current_balance) {
                throw new BalanceMismatchException();
            }

            // Perform balance calculations
            $balance_current_balance = $balance->current_balance;
            $rollback_balance = $balance_current_balance + $balance_division->current_balance;
            $remain_balance = $rollback_balance - $this->convertToFloat($request["initial_balance"]);

            if ($remain_balance < 0) {
                throw new BalanceMismatchException("Unable to update balance division because remain balance is not enough.");
            }

            // Update the balance
            $balance->current_balance = $remain_balance;
            $balance->save();

            // Update the balance division
            $balance_division->current_balance = $this->convertToFloat($request["initial_balance"]);
            $balance_division->initial_balance = $this->convertToFloat($request["initial_balance"]);
            $balance_division->save();

            // Commit the transaction before unlocking the tables
            DB::statement('UNLOCK TABLES');

            return $balance_division;
        } catch (BalanceMismatchException $e) {
            DB::statement('UNLOCK TABLES'); // Ensure tables are unlocked
            throw $e; // Rethrow the exception
        } catch (\Exception $e) {
            DB::statement('UNLOCK TABLES'); // Ensure tables are unlocked
            throw new \Exception("An error occurred while updating the division: " . $e->getMessage());
        }
    }
}


