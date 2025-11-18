<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get chart data for dashboard
     */
    public function chartData(Request $request)
    {
        $user = auth()->user();

        // Base query
        $query = Inspection::query();

        // Filter by inspector for technicians
        if ($user->isTechnician()) {
            $query->where('inspector_id', $user->id);
        } elseif (!$user->isSuperAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Get month offset from request (0 = current month, -1 = previous, 1 = next)
        $monthOffset = (int) $request->get('month_offset', 0);

        // Inspection trends (last 6 months)
        $trendsData = $this->getInspectionTrends($query);

        // Status distribution (for selected month)
        $statusData = $this->getStatusDistribution($query, $monthOffset);

        // Performance metrics
        $metricsData = $this->getPerformanceMetrics($query);

        return response()->json([
            'trends' => $trendsData,
            'status' => $statusData,
            'metrics' => $metricsData,
        ]);
    }

    /**
     * Get inspection trends for the last 6 months
     */
    private function getInspectionTrends($query)
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = (clone $query)
                ->whereYear('inspection_date', $date->year)
                ->whereMonth('inspection_date', $date->month)
                ->count();

            $counts[] = $count;
        }

        return [
            'labels' => $months,
            'values' => $counts,
        ];
    }

    /**
     * Get status distribution for a specific month
     */
    private function getStatusDistribution($query, $monthOffset = 0)
    {
        // Calculate the target month
        $targetDate = Carbon::now()->addMonths($monthOffset);
        $monthName = $targetDate->format('F Y'); // e.g., "November 2025"

        // Filter by the target month
        $monthQuery = (clone $query)
            ->whereYear('inspection_date', $targetDate->year)
            ->whereMonth('inspection_date', $targetDate->month);

        $statuses = ['scheduled', 'completed', 'in_progress', 'cancelled'];
        $labels = [];
        $values = [];

        foreach ($statuses as $status) {
            $count = (clone $monthQuery)->where('status', $status)->count();
            if ($count > 0) {
                $labels[] = ucfirst(str_replace('_', ' ', $status));
                $values[] = $count;
            }
        }

        // If no data, show a message
        if (empty($labels)) {
            $labels = ['No Data'];
            $values = [1];
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'month' => $monthName,
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($query)
    {
        $totalInspections = (clone $query)->count();
        $completedInspections = (clone $query)->where('status', 'completed')->count();

        $completionRate = $totalInspections > 0
            ? round(($completedInspections / $totalInspections) * 100)
            : 0;

        // Average inspections per month (last 6 months)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $recentInspections = (clone $query)
            ->where('inspection_date', '>=', $sixMonthsAgo)
            ->count();
        $avgPerMonth = round($recentInspections / 6, 1);

        return [
            'completionRate' => $completionRate,
            'avgPerMonth' => $avgPerMonth,
        ];
    }
}
