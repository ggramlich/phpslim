package slim;

import java.io.PrintWriter;
import java.io.StringWriter;
import java.lang.reflect.Proxy;

import fitnesse.slim.SlimServer;
import fitnesse.slim.StatementExecutorInterface;

public abstract class Jsr232StatementExecutor implements StatementExecutorInterface{
  private Bridge bridge;
  private Proxy statementExecutorProxy;
  
  public Jsr232StatementExecutor(Bridge bridge) throws Exception
  {
    this.bridge = bridge;
    statementExecutorProxy = bridge.getStatementExecutor();
  }
  
  protected Proxy getStatementExecutorProxy()
  {
    return statementExecutorProxy;
  }
  
  @Override
  public Object addPath(String path) {
    return callMethod("addPath", new Object[] {path});
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
    return callMethod("getInstance", new Object[] {instanceName});
  }

  @Override
  public void setVariable(String name, Object value) {
    callMethod("setVariable", new Object[] {name, value});
  }

  public boolean stopHasBeenRequested() {
    return (Boolean) callMethod("stopHasBeenRequested");
  }

  public void reset() {
    callMethod("reset");
  }

  protected Object callMethod(String method, Object... args) {
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
