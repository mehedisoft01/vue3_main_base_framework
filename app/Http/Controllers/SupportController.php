<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Backend\DashboardController;
use App\Models\ActivityLog;
use App\Models\ActualBudgetLedger\AccountExpenseEntry;
use App\Models\Address\District;
use App\Models\Address\Division;
use App\Models\Address\Upazila;
use App\Models\BudgetEntry\BudgetRevision;
use App\Models\CentralStore;
use App\Models\InterProjectTransfer;
use App\Models\BudgetEntry\ProjectBudget;
use App\Models\MasterSetUp\CompanyInfo;
use App\Models\MasterSetUp\FloorAndStructuralUnit;
use App\Models\MasterSetUp\MaterialCaregory;
use App\Models\MasterSetUp\MaterialCatalogAndUom;
use App\Models\MasterSetUp\ProjectAndBuildingProfile;
use App\Models\MasterSetUp\Uom;
use App\Models\MaterialProcurement\PurchaseOrder;
use App\Models\MaterialProcurement\PurchaseRequisition;
use App\Models\MaterialProcurement\Vendor;
use App\Models\RBAC\Module;
use App\Models\RBAC\Permission;
use App\Models\RBAC\Role;
use App\Models\SiteStore;
use App\Models\User;
use App\Models\WbsWorkSectors\ConstructionSector;
use App\Models\WbsWorkSectors\ExpenseType;
use App\Models\WbsWorkSectors\LaborMaterialMapping;
use App\Models\WbsWorkSectors\WbsPhasingStructure;
use Carbon\Carbon;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use function Carbon\this;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SupportController extends Controller
{
    use Helper;

    public function appConfigurations()
    {
        $role_id = auth()->user()->role_id;
        $user_id = auth()->user()->id;

        $data['user'] = User::where('id', $user_id)->first();
        $data['configs'] = configs(['logo', 'app_name', 'app_logo', 'notify_per_minuit',]);

        $permissions = Permission::whereHas('role_permissions', function ($query) use ($role_id) {
            $query->where('role_id', $role_id);
        })->get();

        $permittedModules = collect($permissions)->pluck('module_id');
        $data['permissions'] = collect($permissions)->pluck('name');

        $locals = [];
        $files = glob(resource_path('lang/*.json'));
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $locals[] = ['locale' => $name, 'name' => $name];
        }
        $data['localization'] = $this->getLocals();

        $data['menus'] = Module::where('parent_id', 0)->where('is_visible', 1)
            ->whereIn('id', $permittedModules)
            ->with(['submenus' => function ($query) use ($permittedModules) {
                $query->with('submenus')->where('is_visible', 1);
                $query->whereIn('id', $permittedModules);
                $query->with(['submenus' => function ($query) use ($permittedModules) {
                    $query->with('submenus')->where('is_visible', 1);
                    $query->whereIn('id', $permittedModules);
                }]);
            }])->get();

        return returnData(2000, $data);
    }

    public function getLocals()
    {
        $locals = [];
        $files = glob(resource_path('lang/*.json'));
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $locals[] = ['locale' => $name, 'name' => $name];
        }

        return $locals;
    }

    public function loadJson()
    {
        $jsonData = [
            'locale' => $this->getLocalization(true),
            'routes' => $this->getRoutes(true),
        ];
        return response()->json(json_encode($jsonData));
    }

    public function addLocalization(Request $request)
    {
        $item = $request->input('item');
        if ($item) {
            try {
                $string = str_replace('_', ' ', $item);
                $file = resource_path('lang/en.json');
                $json = file_get_contents($file);
                $data = json_decode($json, true);
                $data[$item] = ucwords($string);
                $newJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($file, $newJson);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }
    }

    public function getLocalization($inSide = false)
    {
        $locals = [];
        $files = glob(resource_path('lang/*.json'));

        foreach ($files as $file) {
            $locale = pathinfo($file, PATHINFO_FILENAME);
            $locals[$locale] = json_decode(file_get_contents($file), true) ?? [];
        }

        if ($inSide) {
            return $locals;
        }

        return response()->json(json_encode($locals));
    }

    public function getRoutes($inSide = false)
    {
        $select = "id, name, link as path, component, meta, parent_id, icon";
        $routes = [
            [
                "path" => "/",
                "name" => "app",
                "component" => "views/layouts/AppLayouts.vue",
                "children" => Module::selectRaw($select)->where('parent_id', 0)
                    ->with(['children' => function ($query) use ($select) {
                        $query->selectRaw($select);
                        $query->with(['children' => function ($query2) use ($select) {
                            $query2->selectRaw($select);
                        }]);
                    }])->get()
            ],
        ];

        if ($inSide) {
            return $routes;
        }

        return response()->json(json_encode($routes));
    }

    public function getGeneralData()
    {
        $input = request()->all();
        $data = [];
        $user = auth()->user();

        if (isset($input['permissions']) || in_array('permissions', $input)) {
            $key = isset($input['permissions']['key']) ? isset($input['permissions']['key']) : 'permissions';
            $data[$key] = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'status'];
        }

        if (isset($input['roles']) || in_array('roles', $input)) {
            $key = isset($input['roles']['key']) ? isset($input['roles']['key']) : 'roles';
            $data[$key] = Role::where('status', 1)->get();
        }

        if (isset($input['users']) || in_array('users', $input)) {
            $key = isset($input['users']['key']) ?  isset($input['users']['key']) : 'users';
            $data[$key] =  User::where('status', 1)->get();
        }

        if (isset($input['icons']) || in_array('icons', $input)) {
            $data['icons'] = [
                'bx bx-home-alt',
                'bx bx-lock',
                'bx bx-user-circle',
                'bx bx-radio-circle',
                'bx bx-group',
                'bx bx-tachometer',
                'bx bx-receipt',
                'bx bx-credit-card',
                'bx bx-calculator',
                'bx bx-bolt-circle',
                'bx bx-error',
                'bx bx-bar-chart-alt-2',
                'bx bx-cog',
                'bx bx-help-circle',
            ];
        }

        if (isset($input['divisions']) || in_array('divisions', $input)) {
            $key = isset($input['divisions']['key']) ? isset($input['divisions']['key']) : 'divisions';
            $data[$key] = Division::where('status', 1)->get();
        }

        if (isset($input['districts']) || in_array('districts', $input)) {
            $key = isset($input['districts']['key']) ? isset($input['districts']['key']) : 'districts';
            $data[$key] = District::when(isset($input['division_id']), function ($query) use ($input) {
                $query->where('division_id', $input['division_id']);
            })->where('status', 1)->get();
        }

        if (isset($input['upazilas']) || in_array('upazilas', $input)) {
            $key = isset($input['upazilas']['key']) ? isset($input['upazilas']['key']) : 'upazilas';
            $data[$key] = Upazila::where('status', 1)->get();
        }

        if (isset($input['fix_holiday']) || in_array('fix_holiday', $input)) {
            $data['fix_holiday'] = [
                ['name' => 'Shaheed Dibash', 'value' => '02-21', 'checked' => 0],
                ['name' => 'Independence Day', 'value' => '03-26', 'checked' => 0],
                ['name' => 'Pahela Baisakh', 'value' => '04-14', 'checked' => 0],
                ['name' => 'May Day', 'value' => '05-01', 'checked' => 0],
                ['name' => 'Victory Day', 'value' => '12-16', 'checked' => 0],
                ['name' => 'Christmas Day', 'value' => '12-25', 'checked' => 0],
            ];
        }

        return returnData(2000, $data);
    }

    public function appDashboard()
    {
        $dashboardObj = new DashboardController();
        $notificationController = new AppNotificationController();
        $notifications = $notificationController->index(true);
        $dashboard = $dashboardObj->dashboardData();


        return returnData(2000, [
            'dashboard' => $dashboard,
            'notifications' => $notifications,
        ]);
    }

    public function userActivities()
    {
        $data = ActivityLog::orderBy('id', 'DESC')->with('user:id,name')
            ->paginate(input('per_page'));

        return returnData(2000, $data);
    }
}
