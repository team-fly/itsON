package flyapp.its_on;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.widget.Toast;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by HLiu on 05/08/2014.
 */
public class Challenge {

    private static String UPDATE_URL;     //link to php file for updating a challange

    private static final String TAG_SUCCESS = "success";    //string to get integer value of success
    private static final String TAG_MESSAGE = "message";    //key string used to retrieve JSON msgs from PHP

    private static final String TAG_ID = "id";      //key string used for retrieve id from POST in PHP
    private static final String TAG_NAME = "name";  //key string used for retrieve name from POST in PHP
    private static final String TAG_DESCRIPTION = "description";    //key string used for retrieve description from POST in PHP
    private static final String TAG_IMGURL = "url";    //key string used for retrieve description from POST in PHP
    private static final String TAG_FRIENDUSERNAME="friend_username";
    private static final String TAG_ENDDATE="end_date";



    private static final int BUFFER_IO_SIZE = 8000;

    private int ID;
    private String NAME;
    private String DESCRIPTION;
    private Bitmap COVER;
    private Boolean ISTODAYCOMPLETE;
    private String USERNAME;
    private String CLGREQFRIENDUSERNAME;//TODO: no point having two friend user name (duplicate with USER2USERNAME. figure something else later
    private String RETRIEVEDUPDATEMESSAGE="";
    private String ENDDATE;

    private static String URL_DELETECHALLENGE;


    UserSession userSession;

    private Context context;

    private static final ImageHandler imageHandler=new ImageHandler();

    // constructor
    public Challenge(Context arg0) {
        context=arg0;
        UPDATE_URL= context.getResources().getString(R.string.ipaddress) + "update.php";
        userSession = new UserSession(context);

    }

    // constructor
    public Challenge() {
    }

    public boolean loadChallengeInfo(JSONObject data) {
        try {

            ID =Integer.parseInt(data.getString(TAG_ID));
            NAME = data.getString(TAG_NAME);
            DESCRIPTION = data.getString(TAG_DESCRIPTION);
            COVER=imageHandler.loadImageFromUrl(data.getString(TAG_IMGURL));

            USERNAME = userSession.getUserName();

            //TODO: temporary fix for now. handle complete challenge
            if(data.getString("iscomplete")!="null") {
                if (Integer.parseInt(data.getString("iscomplete")) == 1) {
                    ISTODAYCOMPLETE = true;
                } else {
                    ISTODAYCOMPLETE = false;
                }
            }
            else{
                return false;
            }
            return true;

        } catch (JSONException e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean loadChallengeRequest(JSONObject data) {
        try {

            ID =Integer.parseInt(data.getString(TAG_ID));
            NAME = data.getString(TAG_NAME);
            DESCRIPTION = data.getString(TAG_DESCRIPTION);
            COVER=imageHandler.loadImageFromUrl(data.getString(TAG_IMGURL));

            USERNAME = userSession.getUserName();
            CLGREQFRIENDUSERNAME=data.getString(TAG_FRIENDUSERNAME);
            return true;

        } catch (JSONException e) {
            e.printStackTrace();
            return false;
        }
    }


    public boolean loadArchivedChallenges(JSONObject data) {
        try {

            ID =Integer.parseInt(data.getString(TAG_ID));
            NAME = data.getString(TAG_NAME);
            DESCRIPTION=data.getString(TAG_DESCRIPTION);
            ENDDATE = data.getString(TAG_ENDDATE);

            return true;

        } catch (JSONException e) {
            e.printStackTrace();
            return false;
        }
    }



    public int getId() { return ID;}

    public String getName() { return NAME; }

    public String getFriendName() {
        if(CLGREQFRIENDUSERNAME!=null)
            return CLGREQFRIENDUSERNAME;
        else
            return null;

    }

    public String getDescription() { return DESCRIPTION; }

    public Boolean checkIfTodayComplete() { return ISTODAYCOMPLETE;}

    public Bitmap getCoverBitmap() { return COVER;}

    public void setUpdateMessage(String message)
    {
        RETRIEVEDUPDATEMESSAGE=message;
    }

    public void updateToday()
    {
        ISTODAYCOMPLETE=true;
    }

    public void updateTodayOnServer()
    {
        new UpdateChallenge().execute();
    }

    public String getEndDate() { return ENDDATE; }

    private class UpdateChallenge extends AsyncTask<String, String, String > {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... args) {
            //we will develop this method in version 2
            int success;
            int challenge_completed;
            try {
                // Building Parameters
                //HL: do these correspond with the form variable names?
                List<NameValuePair> params = new ArrayList<NameValuePair>();

                int userId = userSession.getUserId();

                params.add(new BasicNameValuePair("user_id", Integer.toString(userId)));
                params.add(new BasicNameValuePair("challenge_id", Integer.toString(ID)));
                params.add(new BasicNameValuePair("update_message", RETRIEVEDUPDATEMESSAGE));

                JSONParser jsonParser = new JSONParser();

                JSONObject json = jsonParser.makeHttpRequest(UPDATE_URL, "POST", params);

                success = json.getInt(TAG_SUCCESS);

                if (success == 1) {
                    ISTODAYCOMPLETE = true;
                    return "Update Successful :)";
                } else {
                    ISTODAYCOMPLETE = false;
                    return "Update Unsuccessful :(";
                }
            } catch (JSONException e) {
                e.printStackTrace();
                return "Error Accessing Database";
            }
        }

        @Override
        protected void onPostExecute(String result) {
             Toast.makeText(context, result, Toast.LENGTH_SHORT).show();
        }

    }





    public void deleteChallenge(BasicNameValuePair[] params, Context context){

        DeleteChallenge deleteChallengeTask=new DeleteChallenge(context);
        deleteChallengeTask.execute(params);
    }


    private class DeleteChallenge extends AsyncTask<BasicNameValuePair, Void, Boolean> {

        private Context mContext;

        public DeleteChallenge(Context context) {
            mContext = context;
        }

        @Override
        protected void onPreExecute() {
            URL_DELETECHALLENGE=mContext.getResources().getString(R.string.ipaddress)+"deletechallenge.php";
            super.onPreExecute();
        }

        @Override
        protected Boolean doInBackground(BasicNameValuePair... paramSet) {
            int success;
            try {
                List<NameValuePair> paramList = new ArrayList<NameValuePair>();

                for(BasicNameValuePair valuePair: paramSet)
                {
                    paramList.add(valuePair);
                }

                JSONParser jsonParser = new JSONParser();
                JSONObject json = jsonParser.makeHttpRequest(URL_DELETECHALLENGE, "POST", paramList);

                success = json.getInt(TAG_SUCCESS);

                if (success == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch (JSONException e) {
                return false;
            }


        }

        @Override
        protected void onPostExecute(Boolean result) {
            super.onPostExecute(result);
            if(result){
                Toast.makeText(mContext, "Challenge Deleted", Toast.LENGTH_SHORT).show();

                Intent intent = new Intent();
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                ((Activity) mContext).finish();

                //Intent friendScreen = new Intent((Activity)mContext, ActivityProfileHome.class);
                //((Activity)mContext).startActivity(friendScreen);
            }else{
                Toast.makeText(mContext, "Error Accessing Server", Toast.LENGTH_SHORT).show();
            }
        }
    }

}
