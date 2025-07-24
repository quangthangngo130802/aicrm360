<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ContractType;
use App\Models\CustomerCategory;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\Result;
use App\Models\Source;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    public function index()
    {
        $sources = Source::pluck('name')->toArray();
        $customerCategory = CustomerCategory::pluck('name')->toArray();
        $channel = Channel::pluck('name')->toArray();
        $result  = Result::pluck('name')->toArray();

        $maxRows = max(
            count($sources),
            count( $customerCategory),
            count( $channel),
            count( $result),
            1
        );

        return view('backend.config.category', compact('sources', 'maxRows', 'customerCategory', 'channel', 'result'));
    }

    public function updateOrCreate(Request $request)
    {

        $request->validate([
            'table' => 'required|in:sources,customer_categories,channels,results',
            'name' => 'required|string|max:255',
            'original_name' => 'nullable|string'
        ]);



        $modelMap = $this->modelMap();
        $model = $modelMap[$request->table];

        // Nếu có original_name => cập nhật
        if (!empty($request->original_name)) {
            $record = $model::where('name', $request->original_name)->first();

            if (!$record) {
                return errorResponse("Không tìm thấy dữ liệu \"{$request->original_name}\" để cập nhật", 404, true);
            }

            // Nếu không đổi tên thì không làm gì
            if ($request->name === $request->original_name) {
                return successResponse("Không có thay đổi.");
            }

            // Kiểm tra trùng tên mới
            if ($model::where('name', $request->name)->where('id', '!=', $record->id)->exists()) {
                return errorResponse("Tên \"{$request->name}\" đã tồn tại!", 422, true);
            }

            $record->update(['name' => $request->name]);

            return successResponse("Cập nhật thành công", $record, 200, true, false);
        }

        // Nếu không có original_name => tạo mới
        if ($model::where('name', $request->name)->exists()) {
            return errorResponse("Tên \"{$request->name}\" đã tồn tại!", 422, true);
        }

        $newRecord = $model::create(['name' => $request->name]);

        return successResponse("Thêm dữ liệu thành công.", $newRecord, 200, true, false);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'table' => 'required|in:sources,customer_categories,channels,results',
            'name' => 'required|string|max:255',
        ]);

        $modelMap = $this->modelMap();

        $model = $modelMap[$request->table];

        $record = $model::where('name', $request->name)->first();

        if ($record) {
            $record->delete();
            return successResponse("Xóa thành công.", $record, 200, true, false);
        }

        return errorResponse("Xóa bản ghi không thành công!", 404);
    }

    private function modelMap(): array
    {
        return [
            'sources' => Source::class,
            'customer_categories' => CustomerCategory::class,
            'channels'  => Channel::class,
            'results'  => Result::class,
        ];
    }
}
