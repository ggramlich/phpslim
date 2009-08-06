package slim;

import java.io.PrintWriter;
import java.io.StringWriter;
import java.lang.reflect.Proxy;

import fitnesse.slim.SlimServer;
import fitnesse.slim.StatementExecutorInterface;

public class PhpStatementExecutor implements StatementExecutorInterface {

  private boolean stopRequested = false;

  private Bridge bridge;
  private Proxy phpStatementExecutorProxy;
  
  public PhpStatementExecutor(Bridge bridge) throws Exception
  {
    this.bridge = bridge;
    phpStatementExecutorProxy = bridge.getStatementExecutor();
  }
  
  private Proxy getStatementExecutor()
  {
    return phpStatementExecutorProxy;
  }
  
  @Override
  public Object addPath(String path) {
    // TODO Auto-generated method stub
    return null;
  }

  @Override
  public Object call(String instanceName, String methodName, Object... args) {
    try {
      return callMethod("call", new Object[] {instanceName, methodName, args});
    } catch (Throwable e) {
      return exceptionToString(e);
    }
  }

  @Override
  public Object create(String instanceName, String className, Object[] args) {
    try {
      return callMethod("create", new Object[] {instanceName, className, args});
    } catch (Throwable e) {
      return exceptionToString(e);
    }
  }

  @Override
  public Object getInstance(String instanceName) {
    try {
      return callMethod("instance", new Object[] {instanceName});
    } catch (Throwable e) {
      return exceptionToString(e);
    }
  }

  @Override
  public void setVariable(String name, Object value) {
    // TODO Auto-generated method stub
    
  }

  public boolean stopHasBeenRequested() {
    return stopRequested;
  }

  public void reset() {
    stopRequested = false;
  }

  private Object callMethod(String method, Object... args) throws Exception {
    return bridge.invokeMethod(getStatementExecutor(), method, args);
  }

  private String exceptionToString(Throwable exception) {
    StringWriter stringWriter = new StringWriter();
    PrintWriter pw = new PrintWriter(stringWriter);
    exception.printStackTrace(pw);
    if (exception.getClass().toString().contains("StopTest")) {
      stopRequested = true;
      return SlimServer.EXCEPTION_STOP_TEST_TAG + stringWriter.toString();
    }
    else {
      return SlimServer.EXCEPTION_TAG + stringWriter.toString();    
    }
  }
}
