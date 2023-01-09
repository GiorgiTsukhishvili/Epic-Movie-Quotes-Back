<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieStoreRequest;
use Spatie\Translatable\HasTranslations;
use App\Models\Movie;

class MovieController extends Controller
{
	use HasTranslations;

	public $translatable = ['name', 'director', 'description'];

	public function index()
	{
		$movies = auth()->user()->movies->withCount('quotes')->get();

		return response()->json(['data' => $movies], 200);
	}

	public function show(Movie $id)
	{
		$movie = $id->with('quotes')->get();

		return response()->json(['movie' => $movie], 200);
	}

	public function store(MovieStoreRequest $request)
	{
		$data = $request->validated();

		if ($request->hasFile('image'))
		{
			$text['image'] = $request->file('image')
			->store('images', 'public');
		}

		$movie = new Movie();

		$movie->setTranslation('name', 'en', $data['name-en'])
		->setTranslation('name', 'ka', $data['name-ka'])->setTranslation('director', 'en', $data['director-en'])
		->setTranslation('director', 'ka', $data['director-ka'])->setTranslation('description', 'en', $data['description-ka'])
		->setTranslation('description', 'ka', $data['description-ka'])->setAttribute('genres', $data['genres'])
		->setAttribute('image', $data['image'])->setAttribute('user_id', auth()->user()->id)->setAttribute('date', $data['date'])
		->save();

		return response()->json(['message' => 'Movie created successfully'], 200);
	}

	public function put(MovieStoreRequest $request, Movie $id)
	{
		$data = $request->validated();

		$newNameTranslations = ['en' => $data['name-ka'], 'ka' => $data['name-en']];

		$newDirectorTranslations = ['en' => $data['director-ka'], 'ka' => $data['director-en']];

		$newDescriptionTranslations = ['en' => $data['description-ka'], 'ka' => $data['description-en']];

		$id->replaceTranslations('name', $newNameTranslations)->replaceTranslations('director', $newDirectorTranslations)
		->replaceTranslations('description', $newDescriptionTranslations)->setAttribute('genres', $data['genres'])
		->setAttribute('image', $data['image'])->setAttribute('user_id', auth()->user()->id)->setAttribute('date', $data['date'])
		->save();

		return response()->json(['message' => 'movie data updated successfully'], 200);
	}

	public function destroy(Movie $id)
	{
		$id->delete();

		return response()->json(['message' => 'movie deleted successfully'], 200);
	}
}