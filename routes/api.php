<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AdminActionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppGuideController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DreamController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InterpretationController;
use App\Http\Controllers\InterpreterController;
use App\Http\Controllers\InterpreterRequestController;

use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSubscriptionCouponController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Middleware For all Routes We want to be authenticated before enter it
Route::middleware(['jwt.verify'])->group(function(){

});

    Route::group([
        'middleware'=>'api',
        'prefix'=>'auth'
    ],function($router){
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/refresh',[AuthController::class,'refresh']);
    Route::get('/user-profile',[AuthController::class,'userProfile']);
    Route::put('/user-profile/update', [AuthController::class, 'updateProfile']);

});



  // App_Guide Controller
    Route::get('guides',[AppGuideController::class,  'index']);
    Route::post('guides',[AppGuideController::class,  'store']);
    Route::get('guide',[AppGuideController::class,  'show']);
    Route::put('guides',[AppGuideController::class,  'update']);
    Route::delete('guides',[AppGuideController::class, 'destroy']);


    // Home route
        // Ads Routes
        Route::get('ads',[AdController::class,  'index']);
        Route::post('ads',[AdController::class,  'store']);
        Route::get('ad', [AdController::class, 'show']);
        Route::put('ads',[AdController::class,  'update']);
        Route::delete('ads',[AdController::class, 'destroy']);

        // I added the sub plans routes down there


        // Chosen Dreams
        Route::get('dreams',[DreamController::class, 'index']);
        Route::post('dreams',[DreamController::class, 'store']);
        Route::get('dream',[DreamController::class, 'show']);
        Route::put('dreams',[DreamController::class, 'update']);
        Route::delete('dreams',[DreamController::class, 'destroy']);

    // Favorite Dreams Page
        // Route to add a dream to favorites
        Route::post('dreams/favorite', [DreamController::class, 'addFavorite']);
        // Route to remove a dream from favorites
        Route::delete('dreams/favorite', [DreamController::class, 'removeFavorite']);
        // Route to get favorite dreams for the authenticated user
        Route::get('user/favorites', [DreamController::class, 'getFavoriteDreams']);
        // Route to get all dreams created by the authenticated user
        Route::get('user/dreams', [DreamController::class, 'getMyDreams']);

        // Chat
         Route::group([
        'prefix'=>'/chat'
    ],function($router){
        // Start a new conversation
        Route::post('/start-conversation', [ChatController::class, 'startConversation']);
        // Send a message (text or voice)
        Route::post('/send-message', [ChatController::class, 'sendMessage']);
        // edit on a message (text)
        Route::post('/edit-message', [ChatController::class, 'editMessage']);
        // Get all messages in a conversation
        Route::get('/get-messages', [ChatController::class, 'getMessages']);

});

    // Interpreter Requests
    Route::post('/interpreter-request', [InterpreterRequestController::class, 'store']);



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::group(['middleware' => ['api', 'checkblocked']], function () {
    // Welcome and Terms
    Route::get('/', 'App\Http\Controllers\WelcomeController@welcome');
    // Route::get('/terms', 'App\Http\Controllers\TermsController@terms'); // don't have controller

    // Authentication Routes
    // Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
    // Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
    // Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->middleware('auth:api');




    // Activation Routes
    Route::get('/activate', 'App\Http\Controllers\Auth\ActivateController@initial');
    Route::get('/activate/{token}', 'App\Http\Controllers\Auth\ActivateController@activate');
    Route::post('/activation/resend', 'App\Http\Controllers\Auth\ActivateController@resend');
    Route::get('/exceeded', 'App\Http\Controllers\Auth\ActivateController@exceeded');

    // Socialite Routes
    Route::get('/social/redirect/{provider}', 'App\Http\Controllers\Auth\SocialController@getSocialRedirect');
    Route::get('/social/handle/{provider}', 'App\Http\Controllers\Auth\SocialController@getSocialHandle');

    // Reactivation
    Route::post('/re-activate/{token}', 'App\Http\Controllers\RestoreUserController@userReActivate');
});

// Authenticated Routes
Route::group(['middleware' => ['auth:api', 'activated', 'activity', 'checkblocked']], function () {
    Route::get('/activation-required', 'App\Http\Controllers\Auth\ActivateController@activationRequired');



    // Profile routes
    Route::get('/profile/{username}', 'App\Http\Controllers\ProfilesController@show');
    Route::post('/profile', 'App\Http\Controllers\ProfilesController@create');
    Route::put('/profile/{username}', 'App\Http\Controllers\ProfilesController@update');
    Route::put('/profile/{username}/account', 'App\Http\Controllers\ProfilesController@updateUserAccount');
    Route::put('/profile/{username}/password', 'App\Http\Controllers\ProfilesController@updateUserPassword');
    Route::delete('/profile/{username}', 'App\Http\Controllers\ProfilesController@deleteUserAccount');

    // Avatar routes
    Route::get('/images/profile/{id}/avatar/{image}', 'App\Http\Controllers\ProfilesController@userProfileAvatar');
    Route::post('/avatar/upload', 'App\Http\Controllers\ProfilesController@upload');
});

// Admin Routes
Route::group(['middleware' => ['auth:api', 'activated', 'role:admin', 'activity', 'checkblocked']], function () {
    // User management
    Route::get('/users', 'App\Http\Controllers\UsersManagementController@index');
    Route::post('/users', 'App\Http\Controllers\UsersManagementController@store');
    Route::get('/users/{user}', 'App\Http\Controllers\UsersManagementController@show');
    Route::put('/users/{user}', 'App\Http\Controllers\UsersManagementController@update');
    Route::delete('/users/{user}', 'App\Http\Controllers\UsersManagementController@destroy');
    Route::post('/users/search', 'App\Http\Controllers\UsersManagementController@search');

    // Deleted users
    Route::get('/users/deleted', 'App\Http\Controllers\SoftDeletesController@index');
    Route::get('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@show');
    Route::put('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@update');
    Route::delete('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@destroy');

    // Themes
    Route::get('/themes', 'App\Http\Controllers\ThemesManagementController@index');
    Route::post('/themes', 'App\Http\Controllers\ThemesManagementController@store');
    Route::get('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@show');
    Route::put('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@update');
    Route::delete('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@destroy');

    // Admin tools
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/routes', 'App\Http\Controllers\AdminDetailsController@listRoutes');
});

Route::apiResource('users', UserController::class);
//Route::apiResource('subscription-plans', SubscriptionPlanController::class);
Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']); // Get all subscription plans
Route::get('subscription-plan', [SubscriptionPlanController::class, 'show']); // Show one subscription plan
Route::post('subscription-plans', [SubscriptionPlanController::class, 'store']); // Store a new subscription plan
Route::put('subscription-plans', [SubscriptionPlanController::class, 'update']); // Update an existing plan
Route::delete('subscription-plans', [SubscriptionPlanController::class, 'destroy']); // Delete a plan
// Route::apiResource('coupons', CouponController::class);
Route::get('coupons', [CouponController::class, 'index']); // Get all coupons
Route::get('coupons/{id}', [CouponController::class, 'show']); // Show one coupon
Route::post('coupons', [CouponController::class, 'store']); // Store a new coupon
Route::put('coupons/{id}', [CouponController::class, 'update']); // Update an existing coupon
Route::delete('coupons/{id}', [CouponController::class, 'destroy']); // Delete a coupon

Route::apiResource('user-subscriptions', UserSubscriptionCouponController::class);
// Route::apiResource('dreams', DreamController::class);
Route::apiResource('admins', AdminController::class);
Route::apiResource('interpreters', InterpreterController::class);
/*
 * GET /api/X/{id}

 */
Route::post('admin-actions', [AdminActionController::class, 'logAction']);  // Log a new admin action
Route::get('admin-actions', [AdminActionController::class, 'index']);       // Get all admin actions
Route::get('admin-actions/{targetType}/{targetId}', [AdminActionController::class, 'getActionsByTarget']);  // Get actions by target (user/dream)
Route::delete('admin-actions/{id}', [AdminActionController::class, 'delete']); // Delete an admin action
/*
 * POST /api/admin-actions
{
    "admin_id": 1,
    "action_type": "ban_user",
    "target_id": 5,
    "target_type": "App\Models\User",
    "details": "Banned the user for inappropriate behavior."
}

 */
Route::post('certifications', [CertificationController::class, 'store']); // Create certification
Route::get('interpreters/{interpreterId}/certifications', [CertificationController::class, 'show']); // Get certifications by interpreter
Route::delete('certifications/{id}', [CertificationController::class, 'destroy']); // Delete certification
/*
 * POST /api/certifications
{
    "interpreter_id": 1,
    "name": "Certified Interpreter",
    "issuing_organization": "Language Association",
    "issue_date": "2023-05-01",
    "credential_id": "12345",
    "credential_url": "http://example.com/credential",
    "credential_img": "file"  # Attach the image file
}

 */
Route::post('interpretations', [InterpretationController::class, 'store']);
Route::get('interpretations', [InterpretationController::class, 'index']); // Get all interpretations
Route::get('interpretations/{id}', [InterpretationController::class, 'show']); // Show one interpretation
Route::get('interpretations/dreams/{dreamId}', [InterpretationController::class, 'indexByDream']); // Get interpretations by dream
Route::get('interpretations/interpreters/{interpreterId}', [InterpretationController::class, 'indexByInterpreter']); // Get interpretations by interpreter
Route::get('interpretations/users/{userId}', [InterpretationController::class, 'indexByUser']); // Get interpretations by user
Route::get('interpretations/approved', [InterpretationController::class, 'indexApproved']); // Get approved interpretations
Route::get('interpretations/unapproved', [InterpretationController::class, 'indexUnapproved']); // Get unapproved interpretations
Route::get('interpretations/approved/{id}', [InterpretationController::class, 'showApproved']); // Show approved interpretation
Route::get('dreams/{dreamId}/interpretation', [InterpretationController::class, 'show']); // Show interpretation of a dream
Route::put('interpretations/{id}/approve', [InterpretationController::class, 'approve']); // Approve interpretation
Route::delete('interpretations/{id}', [InterpretationController::class, 'destroy']); // Delete interpretation
/*
 * POST /api/interpretations
{
    "dream_id": 1,
    "interpreter_id": 2,
    "content": "This dream signifies a new beginning.",
    "is_approved": false
}
GET /api/dreams/1/interpretation (show interpretation of a dream)
PUT /api/interpretations/1/approve (approve it)
DELETE /api/interpretations/1

 */
// Route::middleware('auth:sanctum')->group(function () {
Route::get('feedbacks', [FeedbackController::class, 'index']); // Get all feedbacks
Route::get('feedback', [FeedbackController::class, 'show']); // Show one feedback
Route::post('feedbacks', [FeedbackController::class, 'store']); // Store a new feedback
Route::put('feedbacks', [FeedbackController::class, 'update']); // Update an existing feedback
Route::delete('feedbacks', [FeedbackController::class, 'destroy']); // Delete a feedback
// Route::get('feedbacks/{id}', [FeedbackController::class, 'show']); // Show one feedback
// });

Route::get('complains/user',[ComplainController::class, 'indexUser']);
Route::get('complains', [ComplainController::class, 'index']);
Route::get('complain', [ComplainController::class, 'show']);
Route::post('complains', [ComplainController::class, 'store']);
Route::put('complains', [ComplainController::class, 'update']);
Route::delete('complains', [ComplainController::class, 'destroy']);

// PHP info
Route::get('/phpinfo', function () {
    return phpinfo();
});
