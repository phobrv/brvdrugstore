<?php

namespace Phobrv\BrvDrugstore\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Phobrv\BrvCore\Repositories\PostRepository;
use Phobrv\BrvCore\Repositories\TermRepository;
use Phobrv\BrvCore\Repositories\UserRepository;
use Phobrv\BrvCore\Services\HandleMenuServices;
use Phobrv\BrvCore\Services\UnitServices;
use Phobrv\BrvCore\Services\PostServices;

class DrugstoreController extends Controller
{
    protected $userRepository;
    protected $termRepository;
    protected $postRepository;
    protected $unitService;
    protected $taxonomy;
    protected $type;
    protected $handleMenuService;
    protected $postService;

    public function __construct(
        UserRepository $userRepository,
        TermRepository $termRepository,
        PostRepository $postRepository,
        PostServices $postService,
        UnitServices $unitService,
        HandleMenuServices $handleMenuService
    ) {
        $this->handleMenuService = $handleMenuService;
        $this->userRepository = $userRepository;
        $this->termRepository = $termRepository;
        $this->postRepository = $postRepository;
        $this->postService = $postService;
        $this->unitService = $unitService;
        $this->taxonomy = config('term.taxonomy.region');
        $this->type = config('option.post_type.drugstore');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        //Breadcrumb
        $data['breadcrumbs'] = $this->unitService->generateBreadcrumbs(
            [
                ['text' => 'DrugStores', 'href' => ''],
            ]
        );

        try {
            $data['select'] = $this->userRepository->getMetaValueByKey($user, 'region_select');

            $data['arrayRegion'] = $this->termRepository->getArrayTerms($this->taxonomy);

            if (count($data['arrayRegion']) > 1) {
                unset($data['arrayRegion'][0]);
            }

            if (!$data['select']) {
                $data['select'] = array_search(current($data['arrayRegion']), $data['arrayRegion']);
            }
            $data['regions'] = [];
            $data['drugstores'] = [];
            $data['arrayRegionParent'] = [];
            if ($data['select'] != 0) {
                $data['regions'] = $this->termRepository->find($data['select']);

                $data['drugstores'] = $this->handleMenuService->handleMenuItem($data['regions']->posts);
                $data['arrayRegionParent'] = $this->postService->createArrayMenuParent($data['regions']->posts, 0);
            }

            return view('phobrv::drugstore.index')->with('data', $data);
        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->add(['slug' => $this->unitService->renderSlug($request->title)]);
        $request->validate([
            'slug' => 'required|unique:posts',
        ]);
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['type'] = $this->type;
            $post = $this->postRepository->create($data);
            $post->terms()->attach($data['region_id']);

            $msg = __('Create drugstore success!');

            return redirect()->route('drugstore.index')->with('alert_success', $msg);

        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        //Breadcrumb
        $data['breadcrumbs'] = $this->unitService->generateBreadcrumbs(
            [
                ['text' => 'DrugStores', 'href' => ''],
                ['text' => 'Edit', 'href' => ''],
            ]
        );

        try {

            $data['select'] = $this->userRepository->getMetaValueByKey($user, 'region_select');
            $data['arrayRegion'] = $this->termRepository->getArrayTerms($this->taxonomy);

            if (count($data['arrayRegion']) > 1) {
                unset($data['arrayRegion'][0]);
            }
            if (!$data['select']) {
                $data['select'] = array_search(current($data['arrayRegion']), $data['arrayRegion']);
            }
            $data['regions'] = $this->termRepository->find($data['select']);
            $data['drugstores'] = $this->handleMenuService->handleMenuItem($data['regions']->posts);
            $data['post'] = $this->postRepository->find($id);
            $data['childs'] = $this->postRepository->findByField('parent', $data['post']->id);
            $data['arrayRegionParent'] = $this->postService->createArrayMenuParent($data['regions']->posts, 0);
            return view('phobrv::drugstore.index')->with('data', $data);

        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->request->add(['slug' => $this->unitService->renderSlug($request->title)]);
        $request->validate([
            'slug' => 'required|unique:posts,slug,' . $id,
        ]);
        try {
            $data = $request->all();
            $post = $this->postRepository->update($data, $id);
            $post->terms()->sync($data['region_id']);

            $msg = __('Update drugstore success!');

            return redirect()->route('drugstore.index')->with('alert_success', $msg);

        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }
    public function updateUserSelectRegion(Request $request)
    {
        $user = Auth::user();
        $this->userRepository->insertMeta($user, array('region_select' => $request->select));
        return redirect()->route('drugstore.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
