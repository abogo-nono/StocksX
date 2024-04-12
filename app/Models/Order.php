<?php

namespace App\Models;

use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaracraftTech\LaravelDateScopes\DateScopes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes, DateScopes;

    protected $fillable = [
        'user_id',
        'client_name',
        'client_phone',
        'client_address',
        'total',
        'delivered',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class)
    //         ->withPivot('quantity', 'price')
    //         ->withTimestamps();
    // }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

}
// query by SECONDS
Order::ofJustNow(); // query Orders created just now
Order::ofLastSecond(); // query Orders created during the last second
Order::ofLast15Seconds(); // query Orders created during the last 15 seconds
Order::ofLast30Seconds(); // query Orders created during the last 30 seconds
Order::ofLast45Seconds(); // query Orders created during the last 45 seconds
Order::ofLast60Seconds(); // query Orders created during the last 60 seconds
Order::ofLastSeconds(120); // query Orders created during the last N seconds

// query by MINUTES
Order::ofLastMinute(); // query Orders created during the last minute
Order::ofLast15Minutes(); // query Orders created during the last 15 minutes
Order::ofLast30Minutes(); // query Orders created during the last 30 minutes
Order::ofLast45Minutes(); // query Orders created during the last 45 minutes
Order::ofLast60Minutes(); // query Orders created during the last 60 minutes
Order::ofLastMinutes(120); // query Orders created during the last N minutes

// query by HOURS
Order::ofLastHour(); // query Orders created during the last hour
Order::ofLast6Hours(); // query Orders created during the last 6 hours
Order::ofLast12Hours(); // query Orders created during the last 12 hours
Order::ofLast18Hours(); // query Orders created during the last 18 hours
Order::ofLast24Hours(); // query Orders created during the last 24 hours
Order::ofLastHours(48); // query Orders created during the last N hours

// query by DAYS
Order::ofToday(); // query Orders created today
Order::ofYesterday(); // query Orders created yesterday
Order::ofLast7Days(); // query Orders created during the last 7 days
Order::ofLast21Days(); // query Orders created during the last 21 days
Order::ofLast30Days(); // query Orders created during the last 30 days
Order::ofLastDays(60); // query Orders created during the last N days

// query by WEEKS
Order::ofLastWeek(); // query Orders created during the last week
Order::ofLast2Weeks(); // query Orders created during the last 2 weeks
Order::ofLast3Weeks(); // query Orders created during the last 3 weeks
Order::ofLast4Weeks(); // query Orders created during the last 4 weeks
Order::ofLastWeeks(8); // query Orders created during the last N weeks

// query by MONTHS
Order::ofLastMonth(); // query Orders created during the last month
Order::ofLast3Months(); // query Orders created during the last 3 months
Order::ofLast6Months(); // query Orders created during the last 6 months
Order::ofLast9Months(); // query Orders created during the last 9 months
Order::ofLast12Months(); // query Orders created during the last 12 months
Order::ofLastMonths(24); // query Orders created during the last N months

// query by QUARTERS
Order::ofLastQuarter(); // query Orders created during the last quarter
Order::ofLast2Quarters(); // query Orders created during the last 2 quarters
Order::ofLast3Quarters(); // query Orders created during the last 3 quarters
Order::ofLast4Quarters(); // query Orders created during the last 4 quarters
Order::ofLastQuarters(8); // query Orders created during the last N quarters

// query by YEARS
Order::ofLastYear(); // query Orders created during the last year
Order::ofLastYears(2); // query Orders created during the last N years

// query by DECADES
Order::ofLastDecade(); // query Orders created during the last decade
Order::ofLastDecades(2); // query Orders created during the last N decades

// query by toNow/toDate
Order::secondToNow(); // query Orders created during the start of the current second till now (equivalent of just now)
Order::minuteToNow(); // query Orders created during the start of the current minute till now
Order::hourToNow(); // query Orders created during the start of the current hour till now
Order::dayToNow(); // query Orders created during the start of the current day till now
Order::weekToDate(); // query Orders created during the start of the current week till now
Order::monthToDate(); // query Orders created during the start of the current month till now
Order::quarterToDate(); // query Orders created during the start of the current quarter till now
Order::yearToDate(); // query Orders created during the start of the current year till now
Order::decadeToDate(); // query Orders created during the start of the current decade till now
Order::centuryToDate(); // query Orders created during the start of the current century till now
Order::millenniumToDate(); // query Orders created during the start of the current millennium till now


// query order created today
Order::ofToday();
 // query order created during the last week
Order::ofLastWeek();
 // query order created during the start of the current month till now
Order::monthToDate();
 // query order created during the last year, start from 2020
Order::ofLastYear(startFrom: '2020-01-01');
