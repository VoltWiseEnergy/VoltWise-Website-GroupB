<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Update the authenticated user's monthly budget.
     */
    public function update(Request $request)
    {
        $request->validate([
            'monthly_budget' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ], [
            'monthly_budget.required' => 'Please enter a budget amount.',
            'monthly_budget.numeric'  => 'Budget must be a number.',
            'monthly_budget.min'      => 'Budget cannot be negative.',
            'monthly_budget.max'      => 'Budget amount is too large.',
        ]);

        $user = Auth::user();
        $user->monthly_budget = $request->monthly_budget;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Monthly budget updated successfully!');
    }

    /**
     * Clear / remove the user's monthly budget.
     */
    public function clear()
    {
        $user = Auth::user();
        $user->monthly_budget = null;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Monthly budget removed.');
    }
}
