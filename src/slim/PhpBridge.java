package slim;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.Proxy;

import javax.script.Invocable;
import javax.script.ScriptEngine;
import javax.script.ScriptEngineManager;

import fitnesse.slim.Jsr232Bridge;

public class PhpBridge implements Jsr232Bridge {
  private ScriptEngine engine;
  private Proxy phpProxy;
  
  private static final String ENGINE_NAME = "php-invocable";
  private static final String PHP_DIR = "PhpSlim";
  private static final String STATEMENT_EXECUTOR_METHOD = "getStatementExecutor";
  private static final String GET_PROXY_SCRIPT = "getPhpSlimProxy.php";

  private static final String PHP_VAR_PROXY = "phpProxy";
  private static final String PHP_VAR_PATH = "PHP_PATH";
  
  private File getPhpDirectory() throws IOException {
    File phpDir = new File(PHP_DIR);
    if (!phpDir.exists()) {
      System.out.println("Dir does not exist " + phpDir);
//      return copyFilesFromJar();
      throw new FileNotFoundException("Could not find PhpSlim directory");
    }
    return phpDir;
  }
  
  public String getInternalPhpPath() throws IOException {
    return getPhpDirectory().getAbsolutePath();
  }

  public Proxy getStatementExecutor() throws Exception {
    return (Proxy) invokeMethod(getPhpProxy(), STATEMENT_EXECUTOR_METHOD, new Object[0]);
  }

  public Object invokeMethod(Object thiz, String name, Object... args) throws Exception {
    return getInvocable().invokeMethod(thiz, name, args);
  }
  
  private Proxy getPhpProxy() throws Exception {
    if (phpProxy == null) {
      ScriptEngine engine = getScriptEngine();
      engine.put(PHP_VAR_PATH, getInternalPhpPath());
      engine.eval(new FileReader(getProxyScript()));
      phpProxy = (Proxy) engine.get(PHP_VAR_PROXY);
    }
    return phpProxy;
  }

  private String getProxyScript() throws IOException {
    return getInternalPhpPath() + File.separator + GET_PROXY_SCRIPT;
  }

  public ScriptEngine getScriptEngine() {
    if (engine == null) {
      engine = new ScriptEngineManager().getEngineByName(ENGINE_NAME);
    }
    return engine;
  }
  
  public Invocable getInvocable() {
    return (Invocable) getScriptEngine();
  }
  
//  private File copyFilesFromJar() throws IOException {
//    File tempDir = new File(System.getProperty("java.io.tmpdir"));
//    File phpDir = new File(tempDir, "PhpSlim");
//    phpDir.mkdir();
//    File temporaryFile = new File(phpDir, GET_PROXY_SCRIPT);
//    InputStream templateStream = getClass().getResourceAsStream("Resources/" + GET_PROXY_SCRIPT);
//    copy(templateStream, new FileOutputStream(temporaryFile));
//    System.out.println("Copied to " + temporaryFile + " res " + temporaryFile.exists());
//    return phpDir;
//  }
//  
//  private void copy(InputStream in,
//      FileOutputStream out) throws IOException {
//    byte[] buf = new byte[1024];
//    int len;
//    while ((len = in.read(buf)) > 0){
//      out.write(buf, 0, len);
//    }
//    in.close();
//    out.close();
//    System.out.println("File copied.");
//  }

}
