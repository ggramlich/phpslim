package slim;

import java.io.InputStream;
import java.io.InputStreamReader;
import java.lang.reflect.Proxy;

import javax.script.Invocable;
import javax.script.ScriptEngine;

import fitnesse.slim.Jsr223Bridge;

public class PhpBridge extends Jsr223Bridge {
  private Proxy phpProxy;
  
  private static final String ENGINE_NAME = "php-invocable";
  private static final String STATEMENT_EXECUTOR_METHOD = "getStatementExecutor";
  private static final String GET_PROXY_SCRIPT = "getPhpSlimProxy.php";
  private static final String GET_PROXY_COMPLETE_SCRIPT = "phplib/" + GET_PROXY_SCRIPT;

  private static final String PHP_VAR_PROXY = "phpProxy";
  private static final String PHP_VAR_PATH = "PHP_PATH";

  private String includePath;
  
  public PhpBridge() {
  }

  public PhpBridge(String includePath) {
    this.includePath = includePath;
  }

  public String getIncludePath() {
    return includePath;
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
      engine.put(PHP_VAR_PATH, getIncludePath());
      engine.eval(getProxyScriptAsStreamReader());
      phpProxy = (Proxy) engine.get(PHP_VAR_PROXY);
    }
    return phpProxy;
  }

  private InputStreamReader getProxyScriptAsStreamReader() {
    InputStream in = getClass().getResourceAsStream(GET_PROXY_COMPLETE_SCRIPT);
    return new InputStreamReader(in);
  }

  
  public Invocable getInvocable() {
    return (Invocable) getScriptEngine();
  }

  @Override
  public String getEngineName() {
    return ENGINE_NAME;
  }
}
