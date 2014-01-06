
import android.app.Activity;
import android.os.Bundle;
import android.webkit.WebSettings;
import android.webkit.WebView;

public class MainActivity extends Activity
{
    WebView myWebView;
    
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        myWebView = (WebView) findViewById(R.id.webView1);
        WebSettings webSettings = myWebView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setAllowUniversalAccessFromFileURLs(true);
        webSettings.setAllowFileAccessFromFileURLs(true);
        myWebView.loadUrl("file:///android_asset/index.html");
    }
    
    @Override
    public void onBackPressed() {
        myWebView.loadUrl("javascript:(function() { var esc = $.Event('keydown', { keyCode: 27 }); $('body').trigger(esc);​ })()");
    }
}

