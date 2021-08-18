<?php

namespace App\Http\Controllers\Common;

use App\ApiKey;
use App\Http\Controllers\Common\Twitter\TwitterOAuth;
use App\Http\Controllers\Controller;
use App\Model\Common\SocialMedia;
use Exception;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    protected $social;

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'getTweets']);
        $this->middleware('admin', ['except' => 'getTweets']);

        $social = new SocialMedia();
        $this->social = $social;
    }

    public function index()
    {
        try {
            return view('themes.default1.common.social.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function getSocials()
    {
        try {
            $social = $this->social->get();

            return \DataTables::of($social)
                            ->addColumn('#', function ($model) {
                                return "<input type='checkbox' value=".$model->id.' name=select[] id=check>';
                            })
                            ->addColumn('name', function ($model) {
                                return $model->name;
                            })
                            ->addColumn('class', function ($model) {
                                return $model->class;
                            })
                            ->addColumn('link', function ($model) {
                                return $model->link;
                            })
                            // ->showColumns('name', 'class', 'link')
                            ->addColumn('action', function ($model) {
                                return '<a href='.url('social-media/'.$model->id.'/edit')
                                ." class='btn btn-sm btn-secondary btn-xs'".tooltip('Edit')."<i class='fa fa-edit'
                                 style='color:white;'> </i></a>";
                            })
                            ->rawColumns(['name', 'class', 'link', 'action'])
                            ->make(true);
            // ->searchColumns('name')
                            // ->orderColumns('class')
                            // ->make();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function create()
    {
        try {
            return view('themes.default1.common.social.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'link'     => 'required|url',
            'class'    => 'required',
            'fa_class' => 'required',
        ]);

        try {
            $this->social->fill($request->input())->save();

            return redirect()->back()->with('success', \Lang::get('message.saved-successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $social = $this->social->findOrFail($id);

            return view('themes.default1.common.social.edit', compact('social'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'link'     => 'required|url',
            'class'    => 'required',
            'fa_class' => 'required',
        ]);

        try {
            $social = $this->social->findOrFail($id);
            $social->fill($request->input())->save();

            return redirect()->back()->with('success', \Lang::get('message.updated-successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Response
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('select');
            if (! empty($ids)) {
                foreach ($ids as $id) {
                    $social = $this->social->where('id', $id)->first();
                    if ($social) {
                        $social->delete();
                    } else {
                        echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>"./* @scrutinizer ignore-type */\Lang::get('message.alert').'!</b> 
                    './* @scrutinizer ignore-type */
                    \Lang::get('message.failed').'
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        './* @scrutinizer ignore-type */\Lang::get('message.no-record').'
                </div>';
                        //echo \Lang::get('message.no-record') . '  [id=>' . $id . ']';
                    }
                }
                echo "<div class='alert alert-success alert-dismissable'>
                    <i class='fa fa-ban'></i>

                    <b>"./* @scrutinizer ignore-type */  \Lang::get('message.alert').
                    '!</b> './* @scrutinizer ignore-type */ \Lang::get('message.success').'

                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        './* @scrutinizer ignore-type */\Lang::get('message.deleted-successfully').'
                </div>';
            } else {
                echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>"./* @scrutinizer ignore-type */\Lang::get('message.alert').'!</b> '.
                    /* @scrutinizer ignore-type */\Lang::get('message.failed').'
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        './* @scrutinizer ignore-type */ \Lang::get('message.select-a-row').'
                </div>';
                //echo \Lang::get('message.select-a-row');
            }
        } catch (\Exception $e) {
            echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>"./* @scrutinizer ignore-type */\Lang::get('message.alert').'!</b> '.
                    /* @scrutinizer ignore-type */\Lang::get('message.failed').'
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        '.$e->getMessage().'
                </div>';
        }
    }

    public function getTweets()
    {
        try {
            $tweet_limit = 2;
            $username = 'faveohelpdesk';
            $consumer_key = ApiKey::find(1)->value('twitter_consumer_key');
            $consumer_secrete = ApiKey::find(1)->value('twitter_consumer_secret');
            $access_token = ApiKey::find(1)->value('twitter_access_token');
            $access_token_secrete = ApiKey::find(1)->value('access_tooken_secret');

            $twitter = new TwitterOAuth($consumer_key, $consumer_secrete, $access_token, $access_token_secrete);

            // Migrate over to SSL/TLS
            // Load the Tweets
            $tweets = $twitter->get('statuses/user_timeline',
                ['screen_name' => $username, 'exclude_replies' => 'true',
                    'include_rts'  => 'false', 'count' => $tweet_limit, ]);

            //dd($tweets);
            // Example output
            // Put this after fetching Tweets
            $twitter = '';
            // Create the HTML output
            //dd($tweets[0]->text);
            if (! empty($tweets)) {
                foreach ($tweets as $tweet) {
                    $twitter .= '<li>
                        <span class="status"><i class="fa fa-twitter"></i> '.$tweet->text.'</span>
                        <span class="meta">'.date('g:i a M j', strtotime($tweet->created_at)).'</span>
                     </li>';
                }
            }

            echo '<ul>'.$twitter.'</ul>';
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
            // $ex->getMessage();
        }
    }
}
