<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends BaseController
{
    protected $todo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Todo $todo)
    {
        $this->middleware('auth:api');
        $this->todo = $todo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = $this->todo->latest()->paginate(10);

        return $this->sendResponse($todos, 'Todos list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $todos = $this->todo->get(['name', 'id']);

        return $this->sendResponse($todos, 'Todos list');
    }


    /**
     * Store a newly created resource in storage.
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $todo = $this->todo->create([
            'name' => $request->get('name')
        ]);

        return $this->sendResponse($todo, 'Todo Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $todo = $this->todo->findOrFail($id);

        $todo->update($request->all());

        return $this->sendResponse($todo, 'Todo Information has been updated');
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $todo = $this->todo->findOrFail($id);
        // delete the todo

        $todo->delete();

        return $this->sendResponse([$todo], 'Todos has been Deleted');
    }
}
