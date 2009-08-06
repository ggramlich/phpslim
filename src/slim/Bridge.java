package slim;

import java.lang.reflect.Proxy;

public interface Bridge {

  public abstract Proxy getStatementExecutor() throws Exception;

  public abstract Object invokeMethod(Object thiz, String name, Object... args)
      throws Exception;

}