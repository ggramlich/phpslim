package slim;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.lang.reflect.Proxy;

import javax.script.Invocable;
import javax.script.ScriptEngine;
import javax.script.ScriptEngineManager;

public class PhpBridge {
  private ScriptEngine engine;
  private Proxy phpProxy;
  
  private static final String ENGINE_NAME = "php-invocable";
  private static final String PHP_DIR = "PhpSlim";
  private static final String STATEMENT_EXECUTOR_METHOD = "getStatementExecutor";
  private static final String GET_PROXY_SCRIPT = "getPhpSlimProxy.php";

  private static final String PHP_VAR_PROXY = "phpProxy";
  private static final String PHP_VAR_PATH = "PHP_PATH";
  
  private File getPhpDirectory() throws FileNotFoundException {
    File phpDir = new File(PHP_DIR);
    if (!phpDir.exists()) {
      throw new FileNotFoundException("Could not find PhpSlim directory");
    }
    return phpDir;
  }
  
  public String getInternalPhpPath() throws FileNotFoundException {
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

  private String getProxyScript() throws FileNotFoundException {
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
  
}
