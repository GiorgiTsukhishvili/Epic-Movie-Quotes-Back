<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteStoreRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;

class QuoteController extends Controller
{
	public function index()
	{
		if (!is_null(request('lang')))
		{
			app()->setLocale(request('lang'));
		}

		$quotes = Quote::with(['likes', 'comments' => function ($comment) {
			return $comment->with(['user']);
		}, 'movie' => function ($movie) {
			return $movie->select('movies.id', 'movies.user_id', 'movies.name', 'movies.date')
			->with(['user' => function ($user) {
				return $user->select('users.id', 'users.name', 'users.image');
			}]);
		}])->filter(request(['search']))->orderBy('created_at', 'desc')->paginate(3);

		return response()->json($quotes, 200);
	}

		public function show($id)
		{
			$quote = Quote::where('id', $id)->with(['likes' => function ($like) {
				return $like->with(['user']);
			}, 'comments' => function ($comment) {
				return $comment->with(['user']);
			}])->get();

			return response()->json($quote, 200);
		}

		public function store(QuoteStoreRequest $request)
		{
			$data = $request->validated();

			if ($request->hasFile('image'))
			{
				$text['image'] = $request->file('image')
				->store('images', 'public');
				$data['image'] = asset('storage/' . $text['image']);
			}

			$quote = new Quote();

			$quote->setTranslation('quote', 'en', $data['quote-en'])
			->setTranslation('quote', 'ka', $data['quote-ka'])
			->setAttribute('image', $data['image'])->setAttribute('movie_id', $data['id'])
			->save();

			return response()->json(['message' => 'Quote created successfully'], 200);
		}

		public function put(UpdateQuoteRequest $request)
		{
			$data = $request->validated();

			$newQuoteTranslations = ['en' => $data['quote-en'], 'ka' => $data['quote-ka']];

			if ($request->hasFile('image'))
			{
				$text['image'] = $request->file('image')
				->store('images', 'public');
				$data['image'] = asset('storage/' . $text['image']);
			}

			$quote = Quote::find($request->id);

			$quote->replaceTranslations('quote', $newQuoteTranslations)->setAttribute('image', $data['image'])
			->save();

			return response()->json(['message' => 'quote data updated successfully'], 200);
		}

		public function destroy(Quote $id)
		{
			$id->delete();

			return response()->json(['message' => 'quote deleted successfully'], 200);
		}
}
