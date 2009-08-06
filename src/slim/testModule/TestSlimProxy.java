package slim.testModule;

import fitnesse.slim.StatementExecutorInterface;

public class TestSlimProxy {
  private String testSlim;
  private StatementExecutorInterface caller;

  public TestSlimProxy(String testSlim, StatementExecutorInterface caller) {
    this.testSlim = testSlim;
    this.caller = caller;
  }

  public String niladWasCalled() {
    return (String) caller.call(testSlim, "niladWasCalled"); 
  }

  public String getStringArg() {
    return (String) caller.call(testSlim, "getValue");
  }

  public int getIntArg() {
    return Integer.parseInt((String) caller.call(testSlim, "getValue"));
  }

  public double getDoubleArg() {
    return Double.parseDouble((String) caller.call(testSlim, "getValue"));
  }

  public String getDateArg() {
    return (String) caller.call(testSlim, "getValue");
  }

  public Object getListArg() {
    return caller.call(testSlim, "getListArg");
  }

  public Integer getIntegerObjectArg() {
    return Integer.parseInt((String) caller.call(testSlim, "getIntegerObjectArg"));
  }

  public Double getDoubleObjectArg() {
    return Double.valueOf((String) caller.call(testSlim, "getDoubleObjectArg"));
  }
  
  public Object getCharArg() throws Exception {
    String result = (String) caller.call(testSlim, "getCharArg");
    if (1 != result.length()) {
      throw new Exception("Did not get single character string");
    }
    return result.toCharArray()[0];
  }
  
}
