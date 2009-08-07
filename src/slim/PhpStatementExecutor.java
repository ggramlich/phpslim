package slim;

import java.io.PrintWriter;
import java.io.StringWriter;
import java.lang.reflect.Proxy;

import fitnesse.slim.SlimServer;
import fitnesse.slim.StatementExecutorInterface;

public class PhpStatementExecutor implements StatementExecutorInterface {
  private Bridge bridge;
  private Proxy phpStatementExecutorProxy;
  
  public PhpStatementExecutor(Bridge bridge) throws Exception
  {
    this.bridge = bridge;
    phpStatementExecutorProxy = bridge.getStatementExecutor();
  }
  
  private Proxy getStatementExecutorProxy()
  {
    return phpStatementExecutorProxy;
  }
  
  @Override
  public Object addPath(String path) {
    return callMethod("addModule", new Object[] {path});
  }

  @Override
  public Object call(String instanceName, String methodName, Object... args) {
    return callMethod("call", new Object[] {instanceName, methodName, args});
  }

  @Override
  public Object create(String instanceName, String className, Object[] args) {
    return callMethod("create", new Object[] {instanceName, className, args});
  }

  @Override
  public Object getInstance(String instanceName) {
    return callMethod("instance", new Object[] {instanceName});
  }

  @Override
  public void setVariable(String name, Object value) {
    // Packing the value into a single element array, because a null value
    // caused the proxy call to hang.
    callMethod("setSymbol", new Object[] {name, new Object[] {value}});
  }

  public boolean stopHasBeenRequested() {
    return (Boolean) callMethod("stopHasBeenRequested");
  }

  public void reset() {
    callMethod("reset");
  }

  private Object callMethod(String method, Object... args) {
    try {
      return bridge.invokeMethod(getStatementExecutorProxy(), method, args);
    } catch (Throwable e) {
      return exceptionToString(e);
    }
  }

  private String exceptionToString(Throwable exception) {
    StringWriter stringWriter = new StringWriter();
    PrintWriter pw = new PrintWriter(stringWriter);
    exception.printStackTrace(pw);
    return SlimServer.EXCEPTION_TAG + stringWriter.toString();
  }
}
