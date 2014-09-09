package flyapp.its_on;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import net.danlew.android.joda.JodaTimeAndroid;


import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.joda.time.DateTime;
import org.joda.time.format.DateTimeFormat;
import org.joda.time.format.DateTimeFormatter;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.lucasr.twowayview.TwoWayView;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;


public class ActivityChallengeMainPage extends Activity {

    private static final String PHPTAG_CHALLENGEID="challenge_id";

    class CustomDateVar{
        DateTime date;
        boolean iscomp;
        boolean ishead;
    }

    private List<CustomDateVar> storedDates;

    private static String LOGIN_URL;

    private static final String PHPTAG_SUCCESS = "success";
    private static final String PHPTAG_DATES = "dates";

    //TAG used to retrieve post data containing all the info with regards to the challenge
    private static final String PHPTAG_CHLGINFO = "challenge_info";

    private static final String PHPTAG_DATE = "date";
    private static final String PHPTAG_ISCOMP = "iscomplete";
    private static final String PHPTAG_MSG = "message";

    private final String PHPTAG_USER1NAME="user_1_username";
    private final String PHPTAG_USER1ISCOMP="user_1_iscomplete";
    private final String PHPTAG_USER1MSG="user_1_message";
    private final String PHPTAG_USER1DPURL="user_1_dpurl";
    private final String PHPTAG_USER2NAME="user_2_username";
    private final String PHPTAG_USER2ISCOMP="user_2_iscomplete";
    private final String PHPTAG_USER2MSG="user_2_message";
    private final String PHPTAG_USER2DPURL="user_2_dpurl";

    private String user1Name;
    private boolean user1IsCompleted;
    private String user1Message;
    private Bitmap user1DisplayPicture;

    private String user2Name;
    private boolean user2IsCompleted;
    private String user2Message;
    private Bitmap user2DisplayPicture;

    private ProgressDialog progressDialog;      //dialog used to show task is running

    UserSession userSession;

    //currently selected challenge. retrieved from profile home when clicked on
    Challenge selectedChallenge;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_challenge_main_page);

        LOGIN_URL = getResources().getString(R.string.ipaddress)+ "fillchallenge.php";
        userSession = new UserSession(ActivityChallengeMainPage.this);

        JodaTimeAndroid.init(this);
    }

    @Override
    protected void onResume() {
        // TODO Auto-generated method stub
        super.onResume();

        setTitle("");

        selectedChallenge= ActivityProfileHome.selectedChallenge; //TODO: error handing if null
        storedDates=new ArrayList<CustomDateVar>();

        new LoadChallenge().execute();

    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.challenge_main_page, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.action_deleteFriend:

                BasicNameValuePair[] params=new BasicNameValuePair[1];
                params[0]=new BasicNameValuePair(PHPTAG_CHALLENGEID, Integer.toString(selectedChallenge.getId()));

                Challenge challenge=new Challenge();
                challenge.deleteChallenge(params,this);
                return true;
            default:
                return super.onOptionsItemSelected(item);
        }
    }

    public class DateDispAdapter extends BaseAdapter {


        @Override
        public int getCount() {
            // TODO Auto-generated method stub
            return storedDates.size();
        }

        @Override
        public Object getItem(int arg0) {
            // TODO Auto-generated method stub
            return storedDates.get(arg0);
            //??: what is arg0, arg1....?
        }

        @Override
        public long getItemId(int arg0) {
            // TODO Auto-generated method stub
            return arg0;
        }

        @Override
        public View getView(int arg0, View arg1, ViewGroup arg2) {
            //Q: don't understand what a view is and viewgroup

            final CustomDateVar curItem = storedDates.get(arg0);
            boolean isComp=curItem.iscomp;

            TextView tv_month;
            TextView tv_date;

            if ( !isComp) {
                LayoutInflater inflater = (LayoutInflater) ActivityChallengeMainPage.this.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                arg1 = inflater.inflate(R.layout.view_challengedates_unfilled, arg2, false);

            }
            else if(isComp)
            {
                LayoutInflater inflater = (LayoutInflater) ActivityChallengeMainPage.this.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                arg1 = inflater.inflate(R.layout.view_challengedates_filled, arg2, false);
            }

            if(!isComp)
            {
                tv_month = (TextView) arg1.findViewById(R.id.tv_chlgDateMonthUnfilled);
                tv_date = (TextView) arg1.findViewById(R.id.tv_chlgDateUnfilled);
            }
            else
            {
                tv_month = (TextView) arg1.findViewById(R.id.tv_chlgDateMonthFilled);
                tv_date = (TextView) arg1.findViewById(R.id.tv_chlgDateFilled);
            }

            DateTimeFormatter formatter = DateTimeFormat.forPattern("MMM"); // Make sure we use English month names
            String monthText = formatter.print(curItem.date);

            formatter = DateTimeFormat.forPattern("dd");
            String dayText = formatter.print(curItem.date);

            if(curItem.ishead==true)
                tv_month.setText(monthText);
            else
                tv_month.setText("");
            tv_date.setText(dayText);

            return arg1;
        }

        public CustomDateVar getChlgItem(int position)
        {
            return storedDates.get(position);
        }
    }


    public class LoadChallenge extends AsyncTask<Void, Void, Boolean> {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            //start loading dialog while accessing server
            progressDialog = ProgressDialog.show(ActivityChallengeMainPage.this, "Loading...",
                    "Loading Page", false, false);
        }
        @Override
        protected Boolean doInBackground(Void... arg0) {
            int success;
            try {
                List<NameValuePair> params = new ArrayList<NameValuePair>();
                params.add(new BasicNameValuePair("user_id",Integer.toString(userSession.getUserId())));
                params.add(new BasicNameValuePair("challenge_id", Integer.toString(selectedChallenge.getId())));

                JSONParser jsonParser = new JSONParser();
                JSONObject json = jsonParser.makeHttpRequest(LOGIN_URL, "POST", params);

                success = json.getInt("success");

                if (success == 1) {

                    JSONArray dates = null;
                    JSONArray challengeInfo = null;
                    ImageHandler imageHandler=new ImageHandler();

                     dates = json.getJSONArray(PHPTAG_DATES);

                     if (dates != null) {
                         for (int i = 0; i < dates.length(); i++) {
                             JSONObject c = dates.getJSONObject(i);
                             CustomDateVar date = createDateVar(c.getString(PHPTAG_DATE), Integer.parseInt(c.getString(PHPTAG_ISCOMP)));
                             if (i == 0) {
                                 date.ishead = true;
                             }
                             storedDates.add(date);
                         }
                     }

                     //Retrieve contents about the challenge
                     //including information on each user involved in the challenge and their dp
                     challengeInfo= json.getJSONArray(PHPTAG_CHLGINFO);

                     if (dates != null) {
                         JSONObject c = challengeInfo.getJSONObject(0);

                         //Retrieving User 1 information, display pic regarding the challenge for today
                         user1Name=c.getString(PHPTAG_USER1NAME);
                         user1DisplayPicture=imageHandler.loadImageFromUrl(c.getString(PHPTAG_USER1DPURL));
                         if(Integer.parseInt(c.getString(PHPTAG_USER1ISCOMP))==1){
                             user1IsCompleted=true;
                             user1Message="\""+ c.getString(PHPTAG_USER1MSG)+"\"";
                         }
                         else{
                             user1IsCompleted=false;
                             user1Message="Not yet updated";
                         }

                        //Retrieving User 2 information, display pic regarding the challenge for today
                         user2Name=c.getString(PHPTAG_USER2NAME);
                         user2DisplayPicture=imageHandler.loadImageFromUrl(c.getString(PHPTAG_USER2DPURL));
                         if(Integer.parseInt(c.getString(PHPTAG_USER2ISCOMP))==1){
                             user2IsCompleted=true;
                             user2Message="\""+c.getString(PHPTAG_USER2MSG)+"\"";
                         }
                         else{
                             user2IsCompleted=false;
                             user2Message="Not yet updated";
                         }
                     }
                    return true;
                } else {
                    return false;
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
            return null;
        }

        @Override
        protected void onPostExecute(Boolean result) {
            super.onPostExecute(result);

            if(result) {
                //populating the display for date bubbles
                DateDispAdapter aItems = new DateDispAdapter();
                TwoWayView lvTest = (TwoWayView) findViewById(R.id.twv_dateDisp);
                lvTest.setAdapter(aItems);

                //filling image and text regarding the challenge
                ImageView ivChallengeCover = (ImageView) findViewById(R.id.iv_cover);
                ivChallengeCover.setImageBitmap(selectedChallenge.getCoverBitmap());

                TextView tvChallengeName = (TextView) findViewById(R.id.tv_dpChlgName);
                tvChallengeName.setText(selectedChallenge.getName());

                TextView tvChallengeDescription = (TextView) findViewById(R.id.tv_dpChlgDesc);
                tvChallengeDescription.setText(selectedChallenge.getDescription());

                //filling display pic and user info for user 1 for the challenge
                TextView tvUser1Name = (TextView) findViewById(R.id.tv_chlgPageUser1Name);
                tvUser1Name.setText(user1Name);

                TextView tvUser1Message = (TextView) findViewById(R.id.tv_chlgPageUser1Message);
                tvUser1Message.setText(user1Message);

                ImageView ivUser1Dp = (ImageView) findViewById(R.id.iv_chlgPageUser1Dp);
                ivUser1Dp.setImageBitmap(user1DisplayPicture);

                //filling display pic and user info for user 2 for the challenge
                TextView tvUser2Name = (TextView) findViewById(R.id.tv_chlgPageUser2Name);
                tvUser2Name.setText(user2Name);

                TextView tvUser2Message = (TextView) findViewById(R.id.tv_chlgPageUser2Message);
                tvUser2Message.setText(user2Message);

                ImageView ivUser2Dp = (ImageView) findViewById(R.id.iv_chlgPageUser2Dp);
                ivUser2Dp.setImageBitmap(user2DisplayPicture);
            }
            else
            {
                //in case an error occurred during retrieval from the server
                //go back to profile home page
                Intent profileHomePage = new Intent(getApplicationContext(), ActivityProfileHome.class);
                startActivity(profileHomePage);
            }
            //dismiss the progress dialog
            progressDialog.dismiss();
        }
    }

    public CustomDateVar createDateVar(String dateString, int iscomplete)
    {
        SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
        Date date = null;
        DateTime datetime=null;

        try {
            date = format.parse(dateString);
            datetime = new DateTime(date);
        } catch (Exception e) {
            e.printStackTrace();
        }
        CustomDateVar newDate;

        newDate=new CustomDateVar();
        newDate.date=datetime;

        if(iscomplete==1) {
            newDate.iscomp = true;
        }
        else {
            newDate.iscomp = false;
        }

        if(datetime.getMonthOfYear()!=datetime.minusDays(1).getMonthOfYear())
            newDate.ishead=true;

        return newDate;
    }
}
