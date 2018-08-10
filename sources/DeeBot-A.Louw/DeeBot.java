import java.io.FileInputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.security.KeyManagementException;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.UnrecoverableKeyException;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;
import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.KeyManagerFactory;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.TrustManagerFactory;
import javax.net.ssl.X509TrustManager;
import org.jivesoftware.smack.ConnectionConfiguration.SecurityMode;
import org.jivesoftware.smack.PacketListener;
import org.jivesoftware.smack.SmackException;
import org.jivesoftware.smack.SmackException.NoResponseException;
import org.jivesoftware.smack.SmackException.NotConnectedException;
import org.jivesoftware.smack.XMPPException;
import org.jivesoftware.smack.filter.PacketFilter;
import org.jivesoftware.smack.filter.PacketTypeFilter;
import org.jivesoftware.smack.java7.Java7SmackInitializer;
import org.jivesoftware.smack.packet.IQ;
import org.jivesoftware.smack.packet.Stanza;
import org.jivesoftware.smack.provider.IQProvider;
import org.jivesoftware.smack.provider.ProviderManager;
import org.jivesoftware.smack.roster.Roster;
import org.jivesoftware.smack.tcp.XMPPTCPConnection;
import org.jivesoftware.smack.tcp.XMPPTCPConnectionConfiguration;
import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;	

public class DeeBot  {
	// Class:	Ecovacs DeeBot control code
	// Author:	A.J. Louw / iCTraVi Holding B.V.
	// Version:	0.10
	// History: 	2017-09-03 Added full weblogin support code and debug flag
	//		 	2017-08-00 Started initial code somewhere August 2017 
	
	static XMPPTCPConnection connection = null; 
    /************************************************************************************************************
     * Set variables resulting from the json calls  
     ***********************************************************************************************************/
	// Static Logon Settings
	static String glb_user = "";				// from config
	static String glb_pwd = "";				// from config
	static String glb_country = "";			// from config
	static String glb_resource = "12345678"; //"0b213816" in traces, but random number accepted :) 
	// Resolved logon settings
	static String glb_token = "";
	static String glb_uid = "";
	static String glb_From = glb_uid + "/" + glb_resource;
	// My DeeBots
	static String glb_bot1 = "";				// read from config
	static String glb_bot2 = "";				// read from config
	static String glb_bot3 = "";				// read from config
	// DEBUG Flag
	static boolean glb_debug = true;
	
    /************************************************************************************************************
     * setup xmpp connection and login  
     ***********************************************************************************************************/
	public static void setupXMPP() throws UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException, NotConnectedException
	{
		if (glb_uid.isEmpty() || glb_token.isEmpty() || glb_resource.isEmpty()) {
			System.out.println("ERR: Cannot login - ids are not resolved");
			System.exit(1);
		}
		
		new Java7SmackInitializer().initialize();   
		XMPPTCPConnectionConfiguration connConfig =    XMPPTCPConnectionConfiguration
			   .builder()
	           .setServiceName("ecouser.net")
	           .setHost("lbnl.ecouser.net")
	           .setResource(glb_resource)
	           .setPort(5223)
	           .setDebuggerEnabled(glb_debug)
	           .setCompressionEnabled(false)
	           .setSecurityMode(SecurityMode.required)
	           .setCustomSSLContext(getTLSContext())
	           .setSendPresence(false)
	           .setHostnameVerifier(new HostnameVerifier() {
	        	   		public boolean verify(String arg0, SSLSession arg1) {
	                    return true;
	                }
	           })
	           .setUsernameAndPassword(glb_uid,"0/"+glb_resource+"/"+glb_token).build();

		connection = new XMPPTCPConnection(connConfig);
		try {
		   connection.connect();
		} catch (SmackException | IOException | XMPPException e1) {
		   // TODO Auto-generated catch block
		   e1.printStackTrace();
		}

		if (connection.isConnected()==true)
			System.out.println("Connected");

		Roster roster = Roster.getInstanceFor(connection);
		roster.setRosterLoadedAtLogin(false);		

		try {
		   connection.login();
	    } catch (XMPPException | SmackException | IOException e) {
		   // TODO Auto-generated catch block
		   e.printStackTrace();
	    }
	    
		if (connection.isAuthenticated()==true)
			System.out.println("Authenticated");
	}
   
    /************************************************************************************************************
     * disable ssl checks   
     ***********************************************************************************************************/
	private static final TrustManager[] UNQUESTIONING_TRUST_MANAGER = new TrustManager[]{
			new X509TrustManager() {
				public java.security.cert.X509Certificate[] getAcceptedIssuers(){
	            return null;
	        }
	        public void checkClientTrusted( X509Certificate[] certs, String authType ){}
	        public void checkServerTrusted( X509Certificate[] certs, String authType ){}
	        }
    };
	
    /************************************************************************************************************
     * setup a ssl context / keystore to use - and disable ssl checks   
     ***********************************************************************************************************/
	private static SSLContext getTLSContext() throws KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException, UnrecoverableKeyException, KeyManagementException {

		char[] JKS_PASSWORD = "boguspw".toCharArray();
	    char[] KEY_PASSWORD = "boguspw".toCharArray();

	    KeyStore keyStore = KeyStore.getInstance("JKS");
	    InputStream is = new FileInputStream("/Users/alouw/Downloads/wireshark/bogus.keystore");
	    keyStore.load(is, JKS_PASSWORD);
	    KeyManagerFactory kmf = KeyManagerFactory.getInstance(KeyManagerFactory.getDefaultAlgorithm());
	    kmf.init(keyStore, KEY_PASSWORD);
	    TrustManagerFactory tmf = TrustManagerFactory.getInstance(TrustManagerFactory.getDefaultAlgorithm());
	    tmf.init(keyStore);

	    SSLContext sc = SSLContext.getInstance("TLS");
	    	// real ssl manager
	    sc.init(kmf.getKeyManagers(), tmf.getTrustManagers(), new java.security.SecureRandom());

        // Install the all-trusting trust manager
        sc.init( null, UNQUESTIONING_TRUST_MANAGER, null );
	    return sc;
	}
 
    /************************************************************************************************************
     * Send com:ctl command to host
     ***********************************************************************************************************/
    public static class MyCustomIQ extends IQ {
            String token;

            protected MyCustomIQ(String cmd) {
            super("query","com:ctl");
            token = cmd;
            }

            protected IQChildElementXmlStringBuilder getChildElementXML( IQChildElementXmlStringBuilder xml) {
            	System.out.println("got in getchildemelementxml");
            	return xml;
            }

            protected IQChildElementXmlStringBuilder getIQChildElementBuilder(IQChildElementXmlStringBuilder xml) {
            	xml.rightAngleBracket();
            	String str="<ctl td=\"" + token + "\"/>";
            	xml.append(str);
            	return xml;
            	}
        }	

    /************************************************************************************************************
     * Send com:ctl command to host
     ***********************************************************************************************************/
    	public static class MyCustomIQresp extends IQ {
    		String token;

        protected MyCustomIQresp() {
        super("query","com:ctl");
        }

        protected IQChildElementXmlStringBuilder getIQChildElementBuilder(IQChildElementXmlStringBuilder xml) {
        	xml.rightAngleBracket();
       	return xml;
        	}
    }	

    /************************************************************************************************************
     * Send com:ctl command to host
     ***********************************************************************************************************/
    public static void  apiCtlCall(String cmd,String to) throws NoResponseException {
   	 MyCustomIQ iq = new MyCustomIQ(cmd);
   	 iq.setType(IQ.Type.set);
   	
   	 iq.setTo(to);
   	 iq.setFrom(glb_From);

   	 if (glb_debug) {
   	   	 System.out.println("Sending IQ Message");
   	   	 System.out.println(iq.toString());
   	 }
   	 
   	 // send the request
   	 try {
   			connection.sendPacket(iq);
   		} catch (NotConnectedException e) {
   			// TODO Auto-generated catch block
   			e.printStackTrace();
   		}
   	}     

    /************************************************************************************************************
     * setup a ssl context / keystore to use - and disable ssl checks   
     ***********************************************************************************************************/
    public static class myIQFilter implements PacketFilter{
	 
    public myIQFilter() {
    }
 	@Override
	public boolean accept(Stanza arg0) {
        boolean CustomIQReceived = false;
        if (arg0 instanceof MyCustomIQ){
                        CustomIQReceived = true;
        }
        return true;
 	}
    }
    
    /************************************************************************************************************
     * setup a ssl context / keystore to use - and disable ssl checks   
     ***********************************************************************************************************/
    public static class MyPacketListener implements PacketListener {
	@Override
	public void processPacket(Stanza arg0) throws NotConnectedException {
		String str = arg0.getStanzaId();
	   	try {
		System.out.println("Recv (" + str +"): " + arg0.toXML());
	   	} catch (UnsupportedOperationException e) {
	   		e.printStackTrace();
	}
	   	return;
		// TODO Auto-generated method stub
	}
    }
    /************************************************************************************************************
     * setup a ssl context / keystore to use - and disable ssl checks   
     ***********************************************************************************************************/
    public static class MyIQProvider extends IQProvider<MyCustomIQresp> {

	@Override
	public MyCustomIQresp parse(XmlPullParser arg0, int arg1) throws XmlPullParserException, IOException, SmackException {
		int eventType = arg0.next();
		int iqtype = arg0.getEventType();
		boolean done = false;
		
		System.out.println("IQ Type: " + iqtype + arg0.getText());
		while (eventType != XmlPullParser.END_DOCUMENT && (done=false)) {
            if(eventType == XmlPullParser.START_DOCUMENT) {
                System.out.println("Start document");
            } else if(eventType == XmlPullParser.START_TAG) {
                System.out.println("Start tag "+arg0.getName());
                System.out.println("value: " + arg0.getAttributeValue("",arg0.getName()));
                
                if (!(arg0.getName() == "MID") && !(arg0.getName() == "class"))
                {
                		System.out.println("pro: " + arg0.getAttributeName(0));
                		// batteryinfo response
                		if (arg0.getAttributeName(0) == "power") {
                			System.out.println("pro2: " + arg0.getAttributeValue("","power"));
                		}
                		// deviceinfo response
                		if (arg0.getAttributeName(0) == "v") {
                			System.out.println("pro2: " + arg0.getAttributeValue("","v"));
                		}
                }
            } else if(eventType == XmlPullParser.END_TAG) {
                System.out.println("End tag "+arg0.getName());
                if (arg0.getName() == "iq") done = true;
            } else if(eventType == XmlPullParser.TEXT) {
                System.out.println("Text "+arg0.getText());
            }
            eventType = arg0.next();
           }
           System.out.println("End document");
           
           return new MyCustomIQresp();
	}
	}

    /************************************************************************************************************
     * Load config file  
     * @return 
     ***********************************************************************************************************/
    public static void loadConfig() {
  		JSONParser parser = new JSONParser();
  		JSONArray jsonArray = null;
		try {
			jsonArray = (JSONArray) parser.parse(new FileReader("/Users/alouw/eclipse-workspace/DeeBot/src/config.json"));
		} catch (IOException | ParseException e) {
			// TODO Auto-generated catch block
			System.out.println("ERROR (0200): Could not load config file");
			System.exit(0200);
		}

  		for (Object o : jsonArray) {
  			JSONObject person = (JSONObject) o;

  			glb_user = (String) person.get("username");
 			glb_pwd = (String) person.get("password");
  			glb_country = (String) person.get("country");
  			// Bots
  			glb_bot1 = (String) person.get("bot1");
  			glb_bot2 = (String) person.get("bot2");
  			glb_bot3 = (String) person.get("bot3");
  			
  			if (glb_debug) {
  	 			System.out.println("username   :" + glb_user);
  	  			System.out.println("password   :" + glb_pwd);
  	  			System.out.println("country    :" + glb_country);
  	  			System.out.println("bot1       :" + glb_bot1);
  	  			System.out.println("bot2       :" + glb_bot2);
  	  			System.out.println("bot3       :" + glb_bot3);

  			}
/*
  			JSONArray arrays = (JSONArray) person.get("bots");
  			System.out.println("Array Size: " + arrays.size());
  			for (Object object : arrays) {
  				System.out.println("bots::::" + object);
            }
 */ 
  		}
	}   

    public static void executeCmd(String cmd, String bot) throws UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, NotConnectedException, IOException
    {
		// Do Webcalls to retrieve userid and logon token
		webcalls.retrieveLogonToken();
		
		// Setup connection and logon with info from json calls
		setupXMPP();
		
		// Add listener for IQ messages
		PacketFilter packetFilter=new PacketTypeFilter(org.jivesoftware.smack.packet.IQ.class);
		connection.addPacketListener(new MyPacketListener(),new myIQFilter());

		// Add listener for message packets
		PacketFilter packetFilter2=new PacketTypeFilter(org.jivesoftware.smack.packet.Message.class);
		connection.addPacketListener(new MyPacketListener(),packetFilter2);

		// Add listener for presence packets
		PacketFilter packetFilter3=new PacketTypeFilter(org.jivesoftware.smack.packet.Presence.class);
		connection.addPacketListener(new MyPacketListener(),packetFilter3);

		ProviderManager.addIQProvider("query", "com:ctl", new MyIQProvider());    

		if (cmd.equalsIgnoreCase("start")) {
			// Start AutoClean
			try {
				apiCtlCall("Clean\\\"><clean type=\\\"auto",bot);
			} catch (NoResponseException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		if (cmd.equalsIgnoreCase("return")) {
			try {
				apiCtlCall("Charge\"/><charge type=\"go",bot);
			} catch (NoResponseException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		if (cmd.equalsIgnoreCase("status")) {
			// GetBatteryInfo
			try {
				apiCtlCall("GetBatteryInfo",bot);
			} catch (NoResponseException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	
		//apiCtlCall("GetWorkState",glb_bot2);	// work for class 117, not 126
		/* response
		<iq to="201707265978bce82e91d@ecouser.net/12345678" type="set" id="17856" from="E0000961116700550255@117.ecorobot.net/atom"><query xmlns="com:ctl"><ctl td="PushRobotNotify" type="GoBackCharge" act="Charging"/></query></iq>
		 */
			
		// GetBatteryInfo
		//apiCtlCall("GetBatteryInfo",glb_botJoel);

		//apiCtlCall("GetMapInfo",glb_botEG);  // <ctl id="9563" td="GetMapInfo"/>
		//apiCtlCall("GetBlockTimeState",glb_botEG);
			
		//get firmware version loaded on bot
		//apiCtlCall("GetVersion\" name=\"FW",glb_botJoel);

		//get firmware version loaded on bot
		//apiCtlCall("GetCleanType",glb_botEG);
			
		// GetDeviceInfo for device
		//apiCtlCall("GetDeviceInfo",glb_botJoel);
			
		// set auto map updates
		//apiCtlCall("Map\" last=\"15\" type=\"SetAutoReport\" on=\"1",to);

		// return to charger
		//apiCtlCall("Charge\"/><charge type=\"go",glb_botJoel);
			
		// runBackFromCharger
		// apiCtlCall("PushRobotNotify\" type=\"Wait\" act=\"runBackFromCharger",to);
			
		// Start AutoClean
		//apiCtlCall("Clean\\\"><clean type=\\\"auto",to);		
	}    		
    
    /************************************************************************************************************
     * Main program code  
     ***********************************************************************************************************/
		@SuppressWarnings("deprecation")
		public static void main(String args[]) throws XMPPException, IOException, UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, NotConnectedException {
			String arg_cmd = "";
			String arg_bot = "";
			String exc_bot = "";
			
			if (args.length == 2)
			{
				arg_cmd = args[0];
				arg_bot = args[1];
			}
			
			loadConfig();

			switch (arg_bot) {
			case "bot1": { exc_bot = glb_bot1; break; }
			case "bot2": { exc_bot = glb_bot2; break; }
			case "bot3": { exc_bot = glb_bot3; break; }
			}
			// check if there's a bot configured to run against
			if (exc_bot.isEmpty()) {
				System.out.println("ERROR (0100): could not find the robot: " + arg_bot);
				System.exit(100);
			}
			// check if there's a valid command
			if (arg_cmd.equalsIgnoreCase("start") || arg_cmd.equalsIgnoreCase("return") || arg_cmd.equalsIgnoreCase("status")) {
					executeCmd(arg_cmd,exc_bot);
			}
			else
			{
				System.out.println("ERROR (0101): no valid command: " + arg_cmd);
				System.exit(101);
			}
			// exit main program
			//System.exit(0);
			
		}
}