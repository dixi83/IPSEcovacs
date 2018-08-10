import java.io.IOException;
import java.security.KeyManagementException;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.UnrecoverableKeyException;
import java.security.cert.CertificateException;

import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.util.EntityUtils;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

public class webcalls {

    /************************************************************************************************************
     * JSON Call 1 - Find host to request further calls from
     ***********************************************************************************************************/
    public static HttpResponse jsonCallFindHost_step1(String url) {

        try (CloseableHttpClient httpClient = HttpClientBuilder.create().build()) {
            HttpPost request = new HttpPost(url);
            // Build Request
            JSONObject jsonObj = new JSONObject();
       		jsonObj.put("todo", "FindBest");
       		jsonObj.put("service", "EcoUserNew");
       		String body = jsonObj.toString();

            StringEntity params = new StringEntity(body);
            request.addHeader("content-type", "application/json");
            request.setEntity(params);
            HttpResponse result = httpClient.execute(request);

            String json = EntityUtils.toString(result.getEntity(), "UTF-8");
            try {
            		JSONParser parser = new JSONParser();
                Object resultObject = parser.parse(json);

                if (resultObject instanceof JSONObject) {
                    JSONObject obj =(JSONObject)resultObject;
                    // result is here
                    if (DeeBot.glb_debug) {
                        System.out.println(obj.get("result"));
                        System.out.println(obj.get("ip"));
                        System.out.println(obj.get("port"));
                    	
                    }
                }

            } catch (Exception e) {
                // TODO: handle exception
            }

        } catch (IOException ex) {
        }
        return null;
    }

    /************************************************************************************************************
     * JSON Call 2 - resolve mail address to userid
     * @throws IOException 
     * @throws CertificateException 
     * @throws NoSuchAlgorithmException 
     * @throws KeyStoreException 
     * @throws KeyManagementException 
     * @throws UnrecoverableKeyException 
     ***********************************************************************************************************/
    public static HttpResponse jsonCallResolveUser_step2(String url) throws UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException {
      	
        try (CloseableHttpClient httpClient = HttpClientBuilder.create().build()) 
        {
        		/*
        		 * {"todo":"getVipUserId","loginName":"allouw52@gmail.com","country":"NL"}
        		 */
        		HttpPost request = new HttpPost(url);
        		// Build Request
        		JSONObject jsonObj = new JSONObject();
        		jsonObj.put("todo", "getVipUserId");
        		jsonObj.put("loginName", DeeBot.glb_user);
        		jsonObj.put("country", DeeBot.glb_country);
           	String body = jsonObj.toString();
        		
        		StringEntity params = new StringEntity(body);
            request.addHeader("content-type", "application/json");
            request.setEntity(params);
            HttpResponse result = httpClient.execute(request);

            String json = EntityUtils.toString(result.getEntity(), "UTF-8");
            if (DeeBot.glb_debug) {
                System.out.println(json.toString());
            	}
            try {
            		JSONParser parser = new JSONParser();
                Object resultObject = parser.parse(json);

                if (resultObject instanceof JSONObject) {
                    JSONObject obj =(JSONObject)resultObject;
                    // result is here
                    if (DeeBot.glb_debug) {
                    		System.out.println(obj.get("todo"));
                    		System.out.println(obj.get("result"));
                    		System.out.println(obj.get("userId"));
                    }
                    DeeBot.glb_uid = obj.get("userId").toString();
                }

            } catch (Exception e) {
           		System.out.println(e.getMessage().toString());
            }

        } catch (IOException ex) {
        		System.out.println(ex.getMessage().toString());
        }
        return null;
    }

    /************************************************************************************************************
     * JSON Call 3 - retrieve token for login
     * @throws IOException 
     * @throws CertificateException 
     * @throws NoSuchAlgorithmException 
     * @throws KeyStoreException 
     * @throws KeyManagementException 
     * @throws UnrecoverableKeyException 
     ***********************************************************************************************************/
    public static HttpResponse jsonCallLogin_step3(String url) throws UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException {
      	
        try (CloseableHttpClient httpClient = HttpClientBuilder.create().build()) 
        {
        		/*
        		 * {"meId":"201707265978bce82e91d","resource":"0b213816","last":"","password":"eb819b82e6388f9af0f0844397e837e1","realm":"ecouser.net","todo":"login","country":"NL"}
        		 */
        		HttpPost request = new HttpPost(url);
        		// Build Request
        		JSONObject jsonObj = new JSONObject();
        		jsonObj.put("meId", DeeBot.glb_uid);
        		jsonObj.put("resource", DeeBot.glb_resource);
        		jsonObj.put("last", "");
        		jsonObj.put("password", MD5(DeeBot.glb_pwd));
           	jsonObj.put("realm", "ecouser.net");
           	jsonObj.put("todo", "login");
           	jsonObj.put("country", DeeBot.glb_country);
           	String body = jsonObj.toString();
        		
           	if (DeeBot.glb_debug) {
           		System.out.println("json 3: " + body);
           	}
            StringEntity params = new StringEntity(body);
            request.addHeader("content-type", "application/json");
            request.setEntity(params);
            HttpResponse result = httpClient.execute(request);

            String json = EntityUtils.toString(result.getEntity(), "UTF-8");
            try {
            		JSONParser parser = new JSONParser();
                Object resultObject = parser.parse(json);

                if (resultObject instanceof JSONObject) {
                    JSONObject obj =(JSONObject)resultObject;
                    // result is here
                    if (DeeBot.glb_debug) {
                        System.out.println(obj.get("todo"));
                        System.out.println(obj.get("result"));
                        System.out.println(obj.get("resource"));
                        System.out.println(obj.get("token"));
                    }
                    DeeBot.glb_token = obj.get("token").toString();
                }

            } catch (Exception e) {
           		System.out.println(e.getMessage().toString());
            }

        } catch (IOException ex) {
        		System.out.println(ex.getMessage().toString());
        }
        return null;
    }

    /************************************************************************************************************
     * JSON Call 3 - retrieve token for login
     * @throws IOException 
     * @throws CertificateException 
     * @throws NoSuchAlgorithmException 
     * @throws KeyStoreException 
     * @throws KeyManagementException 
     * @throws UnrecoverableKeyException 
     ***********************************************************************************************************/
    public static HttpResponse jsonCallBind_step5(String url) throws UnrecoverableKeyException, KeyManagementException, KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException {
      	
        try (CloseableHttpClient httpClient = HttpClientBuilder.create().build()) 
        {
        		/*
        		 * {"dt":"Android","app":"ecorobot","tz":"+2","resource":"0b213816","td":"Bind","lang":"en","user":"201707265978bce82e91d@ecouser.net",
        		 * "auth":{"resource":"0b213816","userid":"201707265978bce82e91d","with":"users","token":"RLdT6hmSQuXIW05l1svo7V34kG83JRGT","realm":"ecouser.net"}}
        	 */
        		HttpPost request = new HttpPost(url);
        		// Build Request
        		JSONObject jsonObj = new JSONObject();
        		jsonObj.put("dt", "Android");
        		jsonObj.put("app", "ecorobot");
        		jsonObj.put("tz", "+2");
        		jsonObj.put("resource", DeeBot.glb_resource);
        		jsonObj.put("td", "Bind");
        		jsonObj.put("lang", "en");
        		jsonObj.put("user", DeeBot.glb_uid + "@ecouser.net");
 
        		JSONObject jsonObjAuth = new JSONObject();
    			jsonObjAuth.put("resource", DeeBot.glb_resource);
    			jsonObjAuth.put("userid", DeeBot.glb_uid);
    			jsonObjAuth.put("with", "users");
    			jsonObjAuth.put("token", DeeBot.glb_token);
    			jsonObjAuth.put("realm", "ecouser.net");
    			
    			jsonObj.put("auth", jsonObjAuth);

           	String body = jsonObj.toString();
           	if (DeeBot.glb_debug) {
           		System.out.println("json 4: " + body);
           	}
        		
            StringEntity params = new StringEntity(body);
            request.addHeader("content-type", "application/json");
            request.setEntity(params);
            HttpResponse result = httpClient.execute(request);

            String json = EntityUtils.toString(result.getEntity(), "UTF-8");
            System.out.println(json.toString());
            try {
            		JSONParser parser = new JSONParser();
                Object resultObject = parser.parse(json);

                if (resultObject instanceof JSONObject) {
                    JSONObject obj =(JSONObject)resultObject;
                    // result is here
                    if (DeeBot.glb_debug) {
                    		System.out.println(obj.get("todo"));
                    		System.out.println(obj.get("ret"));
                    }
                }

            } catch (Exception e) {
           		System.out.println(e.getMessage().toString());
            }

        } catch (IOException ex) {
        		System.out.println(ex.getMessage().toString());
        }
        return null;
    }

    //
    //
    //
    public static String MD5(String md5) {
 	   try {
 	        java.security.MessageDigest md = java.security.MessageDigest.getInstance("MD5");
 	        byte[] array = md.digest(md5.getBytes());
 	        StringBuffer sb = new StringBuffer();
 	        for (int i = 0; i < array.length; ++i) {
 	          sb.append(Integer.toHexString((array[i] & 0xFF) | 0x100).substring(1,3));
 	       }
 	        return sb.toString();
 	    } catch (java.security.NoSuchAlgorithmException e) {
 	    }
 	    return null;
 	}
    
    //
    //
    //
    public static String retrieveLogonToken() {
		//
		// Retrieve server 
		// 
		String url  = "https://lbnl.ecouser.net:8006/lookup.do";
		webcalls.jsonCallFindHost_step1(url);
		
		//
		// Retrieve user id
		// 
		url = "https://lbnl.ecouser.net:8000/user.do";
		try {
			webcalls.jsonCallResolveUser_step2(url);
		} catch (UnrecoverableKeyException | KeyManagementException | KeyStoreException | NoSuchAlgorithmException
				| CertificateException | IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		//
		// Retrieve logon token
		// 
		url = "https://lbnl.ecouser.net:8000/user.do";
		try {
			webcalls.jsonCallLogin_step3(url);
		} catch (UnrecoverableKeyException | KeyManagementException | KeyStoreException | NoSuchAlgorithmException
				| CertificateException | IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		//
		// Pre-Bind via webservice
		//
		url = "https://lbnl.ecouser.net:8018/notify_engine.do";
		try {
			jsonCallBind_step5(url);
		} catch (UnrecoverableKeyException | KeyManagementException | KeyStoreException | NoSuchAlgorithmException
				| CertificateException | IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return DeeBot.glb_token;
    }
    
}
