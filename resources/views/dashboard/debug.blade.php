<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Debug</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Dashboard Debug Info</h1>

    <div class="debug">
        <h3>Basic Variables:</h3>
        <p>totalUsers: {{ $totalUsers ?? 'NOT SET' }}</p>
        <p>totalPbs: {{ $totalPbs ?? 'NOT SET' }}</p>
        <p>totalTemplates: {{ $totalTemplates ?? 'NOT SET' }}</p>
        <p>totalAmount: {{ $totalAmount ?? 'NOT SET' }}</p>
    </div>

    <div class="debug">
        <h3>Collections Check:</h3>
        <p>weeklyTrend type: {{ gettype($weeklyTrend ?? 'not set') }}</p>
        <p>weeklyTrend count: {{ isset($weeklyTrend) ? count($weeklyTrend) : 'not set' }}</p>

        <p>topUsers type: {{ gettype($topUsers ?? 'not set') }}</p>
        <p>topUsers count: {{ isset($topUsers) ? count($topUsers) : 'not set' }}</p>

        <p>divisionStats type: {{ gettype($divisionStats ?? 'not set') }}</p>
        <p>divisionStats count: {{ isset($divisionStats) ? count($divisionStats) : 'not set' }}</p>

        <p>recentPbs type: {{ gettype($recentPbs ?? 'not set') }}</p>
        <p>recentPbs count: {{ isset($recentPbs) ? count($recentPbs) : 'not set' }}</p>

        <p>recentActivities type: {{ gettype($recentActivities ?? 'not set') }}</p>
        <p>recentActivities count: {{ isset($recentActivities) ? count($recentActivities) : 'not set' }}</p>
    </div>

    <div class="debug">
        <h3>Auth Info:</h3>
        <p>User: {{ Auth::user()->name ?? 'not authenticated' }}</p>
        <p>Role: {{ Auth::user()->role ?? 'no role' }}</p>
        <p>User ID: {{ Auth::user()->id ?? 'no id' }}</p>
    </div>

    <div class="debug">
        <h3>Environment:</h3>
        <p>Laravel Version: {{ app()->version() }}</p>
        <p>PHP Version: {{ PHP_VERSION }}</p>
        <p>Timestamp: {{ now() }}</p>
    </div>

    <hr>
    <p><a href="/dashboard">Back to Dashboard</a> | <a href="/pb">Go to PB List</a></p>
</body>
</html>
