<?php
namespace App\Http\Controllers;

use App\Services\ConsumptionAnalyzer;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index()
    {
        $analyzer = new ConsumptionAnalyzer(Auth::id(), 30);
        $patterns = $analyzer->analyze();

        return view('recommendations.index', compact('patterns'));
    }
}